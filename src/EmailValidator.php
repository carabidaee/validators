<?php

declare(strict_types=1);


class EmailValidator
{
    public const ERROR_INVALID_FORMAT = 'Invalid format';
    public const ERROR_INVALID_DOMAIN = 'There is no record in DNS with such a domain';

    public bool $checkMXRecords = true;

    private array $emails;
    private array $emailsCorrect;
    private array $errors;

    public function validate(string|array $data): bool
    {
        if (\is_string($data)) {
            $data = [$data];
        }
        $this->emails = $data;

        $isValidFormat = $this->validateFormat();

        $isValidMX = true;
        if ($this->checkMXRecords) {
            $isValidMX = $this->validateMX();
        }

        if (!$isValidFormat || !$isValidMX) {
            return false;
        }

        return true;
    }

    public function getErrors(): ?array
    {
        if (!isset($this->errors)) {
            return null;
        }
        return $this->errors;
    }

    public function getCorrectEmails(): ?array
    {
        if (!isset($this->emailsCorrect)) {
            return null;
        }
        return $this->emailsCorrect;
    }

    private function validateFormat(): bool
    {
        $isValid = true;
        $regexForEmail = "/^[_a-z0-9-\+-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
        foreach ($this->emails as $email) {
            if (\preg_match($regexForEmail, $email) !== 1) {
                $this->errors[] = [
                    'email' => $email,
                    'errorText' => self::ERROR_INVALID_FORMAT
                ];
                $isValid = false;
                continue;
            }
            $this->emailsCorrect[] = $email;
        }
        return $isValid;
    }

    private function validateMX(): bool
    {
        $isValid = true;
        foreach ($this->emailsCorrect as $key => $email) {
            $domain = \substr(\strrchr($email, "@"), 1);
            $res = getmxrr($domain, $mxRecords, $mxWeight);
            if (!$res || \count($mxRecords) === 0) {
                $this->errors[] = [
                    'email' => $email,
                    'errorText' => self::ERROR_INVALID_DOMAIN
                ];
                unset($this->emailsCorrect[$key]);
                $isValid = false;
            }
        }
        return $isValid;
    }
}