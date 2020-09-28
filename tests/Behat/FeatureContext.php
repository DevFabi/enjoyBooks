<?php

namespace App\Tests\Behat;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\MinkExtension\Context\MinkContext;

class FeatureContext extends MinkContext implements MinkAwareContext
{
    /**
     * @BeforeScenario @loginAsUser
     */
    public function loginAsUser()
    {
        $this->visitPath('/login');
        $this->fillField('Email', 'tutuToto@gmail.com');
        $this->fillField('Password', 'Tutu');
        $button = $this->fixStepArgument('submit');
        $this->getSession()->getPage()->pressButton('submit');
    }

    /**
     * @BeforeScenario @loginAsAdmin
     */
    public function loginAsAdmin()
    {
        $this->visitPath('/login');
        $this->fillField('Email', 'admin@gmail.com');
        $this->fillField('Password', 'tutu');
        $button = $this->fixStepArgument('submit');
        $this->getSession()->getPage()->pressButton('submit');
    }

    /**
     * @Given /^I wait (\d+) seconds$/
     */
    public function iWaitSeconds($seconds)
    {
        sleep($seconds);
    }

    /**
     * @Given /^I fill "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function iFillFieldWithValue($field, $value)
    {
        $this->fillField($field, $value);
    }

    /**
     * afterTheStep.
     *
     * If the current step is failing, then print the html code of
     * the current page and, if the driver is an instance of
     * Selenium2Driver, print a screenshot of the current page.
     *
     * @AfterStep
     */
    public function afterTheStep(AfterStepScope $scope): ?self
    {
        if (99 !== $scope->getTestResult()->getResultCode()) {
            return null;
        }

        $filePath = __FILE__ . '../debug/behat/';
        // override if is set into behat.yml

        $fileName = date('d-m-y') . '_' . basename($scope->getFeature()->getfile()) . '_' . hash('md5', $scope->getStep()->getText());
        $this->takeScreenshot($filePath, $fileName);
        $this->takePageContent($filePath, $fileName);
        $this->getErrorPosition($filePath, $fileName, $scope);

        return $this;
    }

    private function takeScreenshot(string $filePath, string $fileName): self
    {
        $driver = $this->getSession()->getDriver();

        if (!$driver instanceof Selenium2Driver) {
            return $this;
        }

        $filePath = $filePath . 'screenshot/';
        $extension = '.png';
        $fullPath = $filePath . $fileName . $extension;

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $this->saveScreenshot($fileName . $extension, $filePath);
        echo sprintf(
            "Result screenshot at: %s \n",
            $fullPath
        );

        return $this;
    }

    private function takePageContent(string $filePath, string $fileName): self
    {
        $filePath = $filePath . 'report/';
        $extension = '.txt';
        $fullPath = $filePath . $fileName . $extension;

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        file_put_contents($fullPath, $this->getSession()->getPage()->GetContent());
        echo sprintf(
            "Result HTML page content at: %s \n",
            $fullPath
        );

        return $this;
    }

    private function getErrorPosition(string $filePath, string $fileName, AfterStepScope $scope): self
    {
        // eval(\Psy\sh());
        $filePath = $filePath . 'position/';
        $extension = '.txt';
        $fullPath = $filePath . $fileName . $extension;
        $fileContent = sprintf(
            "Error in file \n'%s'\n
        Title: '%s'\n
        Description:\n'%s'\n
        Line %s : '%s'\n
        Exception:\n'%s'\n",
            $scope->getFeature()->getFile(),
            $scope->getFeature()->getTitle(),
            $scope->getFeature()->getDescription(),
            $scope->getStep()->getLine(),
            $scope->getStep()->getText(),
            $scope->getTestResult()->getException()->getMessage()
        );

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        file_put_contents($fullPath, $fileContent);
        echo sprintf(
            "Position of error at: %s \n",
            $fullPath
        );

        return $this;
    }
}
