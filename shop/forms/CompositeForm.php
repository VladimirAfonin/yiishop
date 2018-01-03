<?php
namespace shop\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;

abstract class CompositeForm extends  Model
{
    /* Model[] */
    private $forms = [];

    /**
     * @return array
     */
    abstract protected function internalForms(): array;

    /**
     * load many forms at once
     *
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null): bool
    {
        $success = parent::load($data, $formName);
        foreach($this->forms as $name => $form) {
            if(is_array($form)) {
                Model::loadMultiple($form, $data);
            } else {
                $success = $form->load($data, $formName !== '' ? null : $name) && $success;
            }
        }
        return $success;
    }

    /**
     * validate many forms at once
     *
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        $parentNames = array_filter($attributeNames, function($name) { // 2-nd: 'array_filter($attributeNames, 'is_string')'
            return is_string($name);
        });
        $success = parent::validate($parentNames, $clearErrors);
        foreach($this->forms as $name => $form) {
            if(is_array($form)) {
                foreach($form as $itemName => $itemForm) {
                    $innerNames = ArrayHelper::getValue($attributeNames, $itemName);
                    $success = $itemForm->validate($innerNames, $clearErrors) && $success;
                }
            } else {
                $innerNames = ArrayHelper::getValue($attributeNames, $name);
                $success = $form->validate($innerNames, $clearErrors) && $success;
            }

        }
        return $success;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if(isset($this->forms[$name])) {
            return $this->forms[$name];
        }
        return parent::__get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->internalForms(), true)) {
            $this->forms[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->forms[$name]) || parent::__isset($name);
    }


}