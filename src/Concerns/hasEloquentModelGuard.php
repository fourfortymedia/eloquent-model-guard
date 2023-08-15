<?php

namespace FourFortyMedia\EloquentModelGuard\Concerns;


use FourFortyMedia\EloquentModelGuard\Attributes\OnCreateRules;
use FourFortyMedia\EloquentModelGuard\Attributes\OnUpdateRules;
use FourFortyMedia\EloquentModelGuard\Exceptions\InvalidModelException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use ReflectionClass;
use Throwable;

/**
 *
 */
trait hasEloquentModelGuard{

    /**
     * Model validation rules
     *
     * @var  array
     */
    protected array $rules = [];

    protected bool $autoValidate = true;

    /**
     * @return void
     */
    protected static function bootUseEloquentModelGuard(): void
    {
        static::updating(function (self $model){
            $model->getModelRules();
            $model->getPropertyRules();
            $model->validate();
        });
        static::creating(function (self $model){
            $model->getModelRules(useOnCreateRules: false);
            $model->getPropertyRules(useOnCreateRules: false);
            $model->validate();
        });
    }

    /**
     * @param callable|null $callback
     *
     * @return Model
     * @throws InvalidModelException
     * @throws Throwable
     */
    public function validate(callable $callback = null): Model
    {
        // get model rules
        $this->getModelRules();
        // get property rules
        $this->getPropertyRules();


        if (!is_null($callback)) {
            $rules = $callback($this->rules);
            $this->rules = tap($rules, fn($value) => throw_unless(is_array($value), new InvalidModelException('The validate callback should return an array')));
        }
        else if(!empty($this->rules)){
            $validator = Validator::make($this->attributesToArray(), $this->rules);
            if ($validator->fails()) {
                $rules = $validator->errors()->all();
                throw new InvalidModelException($rules);
            }
        }

        return $this;
    }

    /**
     * Get the validation rules for the model fields decorator.
     *
     * @param bool $useOnCreateRules
     *
     * @return Model
     */
    public function getModelRules(bool $useOnCreateRules = true): Model
    {
        $decorator = new ReflectionClass($this);
        $class = $useOnCreateRules ? OnCreateRules::class : OnUpdateRules::class;
        $attributes = $decorator->getAttributes($class);

        if (!empty($attributes)) {
            $modelRules = $attributes[0]->newInstance();

            $rules = $modelRules->rules;
            foreach ($rules as $field => $rule){

                $this->rules[$field] = $rule;
            }
        }
        return $this;
    }

    /**
     * Get the validation rules for the model properties decorated with OnUpdateRules attribute.
     *
     * @param bool $useOnCreateRules
     *
     * @return Model
     */
    public function getPropertyRules(bool $useOnCreateRules = true): Model
    {
        $class = new ReflectionClass($this);
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $class = $useOnCreateRules ? OnCreateRules::class : OnUpdateRules::class;
            $attributes = $property->getAttributes($class);
            if (!empty($attributes)) {
                $propertyName = $property->getName();
                $rules = $attributes[0]->newInstance()->rules;
                if(is_array($rules) && Arr::isAssoc($rules)){
                    if(!Arr::has($rules, $propertyName)){
                        throw new \InvalidArgumentException("Invalid rules used on property $propertyName");
                    }else{
                        $rules = $rules[$propertyName];
                    }
                }elseif(is_string($rules)){
                    $this->rules[$propertyName] =  [$rules];
                }else{
                    $this->rules[$propertyName] =  $rules;
                }

            }
        }

        return $this;
    }

}
