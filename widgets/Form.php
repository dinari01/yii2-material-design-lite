<?php

namespace jonasw91\mdl\widgets;

use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * Description:
 *
 * @link https://getmdl.io/components/index.html#badges-section
 *
 * Class Form
 * @package jonasw91\mdl\widgets
 *
 * @author Jonas Wehner <jonaswehner@outlook.de>
 * @version 1.0
 */
class Form extends ActiveForm
{
    public $fieldClass = 'jonasw91\mdl\widgets\Field';

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @param array $options
     * @return Field
     */
    public function field($model, $attribute, $options = [])
    {
        $config = $this->fieldConfig;
        if ($config instanceof \Closure) {
            $config = call_user_func($config, $model, $attribute);
        }
        if (!isset($config['class'])) {
            $config['class'] = $this->fieldClass;
        }
        return \Yii::createObject(ArrayHelper::merge($config, $options, [
            'model' => $model,
            'attribute' => $attribute,
            'form' => $this,
        ]));
    }

    public function init()
    {
        parent::init();

        $this->registerDisabledOnClick();
    }

    private function registerDisabledOnClick()
    {
        $this->view->registerJs(new JsExpression('
            var form' . str_replace('-', '', $this->options['id']) . ' =  $("#' . $this->options['id'] . '");
            form' . str_replace('-', '', $this->options['id']) . '.on("beforeSubmit", function(){
                $("button[type=\'submit\']").prop ("disabled","true");
                return true;
            });
        '));
    }
}