# Validators

---

## Requirements

---

* PHP >= 8.0

## Installation

---

Add `carabidaee/validators` as a require dependency in your `composer.json` file:

```
composer require carabidaee/validators
```

## List of validators

---

* EmailValidator

## Usage

---

Example of using EmailValidator: 

```php
$emailsList = [
    'test@gmail.com',
    // ... list of emails
];

$emailValidator = new EmailValidator();
$emailValidator->checkMXRecords = false; // Disabling verification of MX records

if (!$emailValidator->validate($emailsList)) {
    foreach ($emailValidator->getErrors() as $error) {
        echo "Email {$error->email} incorrect: {$error->errorText}\n";
    }
}

foreach ((array) $emailValidator->getCorrectEmails() as $email) {
    echo "Email {$email} correct\n"; 
}
```