<?php

namespace jonasw91\mdl\widgets;

use jonasw91\mdl\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 *  Options:
 *  - type      string element type
 *  - action    string|array url
 *  - method    string GET or POST
 *  - recall    string JSExpression result
 *  Example:
 *  ```php
 *  'recall' => new JSExpression ('function (response) {
 *      // Do something
 *  }');
 *  ```
 *  - label     string button value
 *  - icon      string you can see possible icon at @link https://design.google.com/icons/
 *  - effects   array item effects set key to true active effect
 *      key: accent, colored, primary, rippleEffect
 *
 * Class Button
 * @package jonasw91\mdl\widgets
 */
class Button extends MdlWidget
{
    const TYPE_DEFAULT = 'mdl-button mdl-js-button';
    const TYPE_FAB = 'mdl-button mdl-js-button mdl-button--fab';
    const TYPE_MINI_FAB = 'mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab';
    const TYPE_RAISED = 'mdl-button mdl-js-button mdl-button--raised';
    const TYPE_ICON = 'mdl-button mdl-js-button mdl-button--icon';

    /**
     *
     * @var array Default values for attribute @attribute $mdlOptions
     *
     *  - type string element type
     *  - action string|array url
     *  - method string GET or POST
     *  - recall string JSExpression result
     *  Example:
     *  ```php
     *  'recall' => new JSExpression ('function (response) {
     *      // Do something
     *  }');
     *  ```
     *  - label string button value
     *  - icon string you can see possible icon at @link https://design.google.com/icons/
     *  - effects array item effects set key to true active effect
     *      key: accent, colored, primary, rippleEffect
     */
    public $defaultMdlOptions = [
        'type' => self::TYPE_DEFAULT,
        'action' => null,
        'method' => 'GET',
        'recall' => '',
        'label' => 'Button',
        'icon' => null,
        'tooltip' => null,
        'confirm' => null,
        'effects' => [
            'accent' => true,
            'colored' => false,
            'primary' => false,
            'rippleEffect' => true,
        ]
    ];

    /**
     * @var array Widget types
     */
    protected $types = [
        self::TYPE_DEFAULT,
        self::TYPE_FAB,
        self::TYPE_MINI_FAB,
        self::TYPE_RAISED,
        self::TYPE_ICON,
    ];

    /**
     * @var array Widget effects
     */
    protected $effects = [
        'accent' => 'mdl-button--accent',
        'colored' => 'mdl-button--colored',
        'primary' => 'mdl-button--primary',
        'rippleEffect' => 'mdl-js-ripple-effect',
    ];

    protected $label;

    public function init()
    {
        parent::init();

        $this->initMdlComponent();

        $mdlOptions = $this->mdlOptions;

        // set action
        if (!is_null($mdlOptions['action'])) {
            if (strtoupper($mdlOptions['method']) === 'GET') {
                $this->view->registerJs(new JsExpression('$("#' . $this->options['id'] . '").click(function (){
                    window.location.href = "' . Url::to($mdlOptions['action']) . '";
                });'));
            } else if (strtoupper($mdlOptions['method']) === 'POST') {
                $this->view->registerJs(new JsExpression('$("#' . $this->options['id'] . '").click(function (){
                    $.post("' . Url::to($mdlOptions['action']) . '", function(resp) {
                       location.reload();
                    });
                });'));
            }
            $this->registerDisabledOnClick();
        }
        // set effects
        foreach ($this->effects as $effect => $value) {
            if (isset($mdlOptions['effects'][$effect]) && $mdlOptions['effects'][$effect]) {
                $this->options ['class'] .= ' ' . $value;
            }
        }
    }

    /**
     * @return string
     */
    public function run()
    {
        $tooltip = '';
        if (isset($this->mdlOptions['tooltip']['text'])) {
            $type = Tooltip::TYPE_DEFAULT;
            $position = Tooltip::POSITION_TOP;
            if (isset($this->mdlOptions['tooltip']['type'])) {
                $type = $this->mdlOptions['tooltip']['type'];
            }
            if (isset($this->mdlOptions['tooltip']['position'])) {
                $position = $this->mdlOptions['tooltip']['position'];
            }
            $tooltip = Tooltip::widget([
                'mdlOptions' => [
                    'referenceID' => $this->id,
                    'text' => $this->mdlOptions['tooltip']['text'],
                    'type' => $type,
                    'position' => $position
                ]
            ]);
        }

        $icon = Html::tag('i', $this->mdlOptions['icon'], [
            'class' => 'material-icons'
        ]);

        $label = Html::tag('span', $this->mdlOptions['label'], [
            'class' => 'mdl-button-label'
        ]);

        if (isset($this->mdlOptions['confirm'])) {
            $confirmMessage = $this->mdlOptions['confirm'];
            echo Dialog::widget([
                'mdlOptions' => [
                    'toggleButtonID' => $this->options['id'],
                    'content' => $confirmMessage,
                    'header' => 'E-Mail Adressen: ',
                    'size' => Dialog::SIZE_LARGE,
                    'actionCancelText' => 'abbrechen'
                ]
            ]);
        }

        return Html::button($icon . $label . $tooltip, $this->options);
    }

    private function registerDisabledOnClick()
    {
        $this->view->registerJs(new JsExpression('
            var button' . str_replace('-', '', $this->options['id']) . ' =  $("#' . $this->options['id'] . '");
            button' . str_replace('-', '', $this->options['id']) . '.click(function(){
                $(this).prop ("disabled","true");
                return false;
            });
        '));
    }
}