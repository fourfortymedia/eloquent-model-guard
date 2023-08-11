# Eloquent Model Guard - Readme

Welcome to the readme for the **eloquent-model-guard** Laravel package. This package brings an array of enhancements to data validation for Eloquent models, providing developers with improved control over data integrity and validation rules. Let's dive into the details of this package.

## Attributes

The **eloquent-model-guard** package introduces the ability to add structured and machine-readable metadata information to declarations. This enhances the control over data validation, particularly within Eloquent models, and allows for finer customization of validation rules based on specific scenarios.

## Background

Database fields are characterized by types that define the nature of data they can store. These types ensure data integrity and consistency by enforcing validation rules. Typically, these setups are performed within migrations. Different data types represent various kinds of data such as numbers, strings, and dates. By indicating the type of a field, the database can validate and enforce rules to ensure only valid data is stored. This not only prevents errors but also enhances query performance and data accuracy.

While migrations establish data integrity upon storage, they are executed only once. The **eloquent-model-guard** package empowers users to update and adapt validation rules within the model. This offers a significant advantage in responding to changing application requirements. Developers can now effortlessly customize validation rules based on evolving business needs without altering the database structure. This dynamic and agile approach enhances the efficiency and adaptability of validation, resulting in a more robust and responsive application.

## Added Features

The **eloquent-model-guard** package comes with several notable additions:

- **Custom Validation Rules:** Developers can define validation rules using custom attributes: `OnCreateRules` and `OnUpdateRules`. This customization allows fine-tuning of validation rules based on specific scenarios.
- **`validateUsing` Method:** The `Model` class now includes a `validateUsing` method, supporting the use of custom validation rules.
- **`protected array $rules;` Property:** A new property has been added to conveniently store validation rules.

## Changes

The following changes have been made to improve validation in Eloquent models:

- The `booted` method within the `Model` class now automatically triggers validation during creation and updating.
- A new static call method `validateUsing` has been introduced within the `__callStatic` method. This method can be used dynamically and statically.

## Context

The enhancements brought by the **eloquent-model-guard** package greatly enhance data validation for Eloquent models. The introduction of `OnCreateRules` and `OnUpdateRules` attributes allows developers to craft specific validation rules for model creation and updates, respectively. This granularity in validation empowers developers to fine-tune validation for individual models, properties, or attributes, promoting cleaner code organization and more efficient validation logic.

## Examples

Let's explore some usage examples to demonstrate how to take advantage of the new attributes:

1. **Validation on Creation:**

   ```php
   use Illuminate\Database\Eloquent\Validations\OnCreateRules;

   #[OnCreateRules(['name' => 'required', 'email' => 'required|email'])]
   class User extends Model {
       // ...
   }
   ```

2. **Validation on Update:**

   ```php
   use Illuminate\Database\Eloquent\Validations\OnUpdateRules;

   #[OnUpdateRules(['email' => 'required|email'])]
   class User extends Model {
       // ...
   }
   ```

3. **Combining Both Attributes:**

   ```php
   use Illuminate\Database\Eloquent\Validations\OnUpdateRules;

   #[OnUpdateRules(['email' => 'required|email'])]
   #[OnCreateRules(['name' => 'required', 'email' => 'required|email'])]
   class User extends Model {
       // ...
   }
   ```

4. **Using on Model Fields:**

   ```php
   use Illuminate\Database\Eloquent\Validations\OnUpdateRules;

   #[OnUpdateRules(['email' => 'required|email'])]
   #[OnCreateRules(['name' => 'required', 'email' => 'required|email'])]
   class User extends Model {
       #[OnUpdateRules(['required'])]
       protected string $name;
       
       #[OnUpdateRules(['email' => 'required|email'])]
       #[OnCreateRules('required|email')]
       protected string $email;
   }
   ```
       please note that when using it on a sigle property, you may pass and accosiative array with a key that is
   the same as the property with the value as the rules or pass rules as array or keys

5. **Custom Rules with Validation:**

   ```php
   $product = Product::validateUsing(function ($rules) {
       $rules['price'] = 'numeric|min:0';
       return $rules;
   })->create($data);
   ```

6. **Automatic Validation on Creation and Updating:**

   ```php
   protected static function booted()
   {
       static::creating(function (self $model) {
           $model->getModelRules(useOnCreateRules: false);
           $model->getPropertyRules(useOnCreateRules: false);
           $model->validate();
       });
       static::updating(function (self $model) {
           $model->getModelRules();
           $model->getPropertyRules();
           $model->validate();
       });
   }
   ```

## Note

Please note that this pull request is a work in progress and is being submitted for initial review and feedback. Further improvements and adjustments will be made based on the feedback received.

Remember that the package's features only take effect when the attributes are applied to the model or properties. This package does not enforce any developer to use it, offering flexibility while providing powerful validation capabilities.

For any inquiries or feedback, feel free to contact Mr. Rikhotso, the creator of this package. Your thoughts and suggestions are highly valued.