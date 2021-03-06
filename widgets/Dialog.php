<?php

namespace jonasw91\mdl\widgets;

use yii\web\JsExpression;
use jonasw91\mdl\helpers\Html;

/**
 * Class Dialog
 *
 * @package jonasw91\mdl\widgets
 */
class Dialog extends MdlWidget
{
    const TYPE_DEFAULT = 'mdl-dialog';
    const TYPE_FULL_WIDTH_ACTIONS = ' mdl-dialog';

    const SIZE_SMALL = 280;
    const SIZE_MEDIUM = 420;
    const SIZE_LARGE = 560;

    public $defaultMdlOptions = [
        'type' => self::TYPE_DEFAULT,
        'size' => self::SIZE_SMALL,
        'header' => 'Header',
        'content' => 'Content',
        'toggleButtonID' => null,
        'toggleButtonOptions' => [

        ],
        'toggleButtonMdlOptions' => [

        ],
        'actions' => [

        ],
        'actionCancelText' => 'Abbrechen'
    ];

    /**
     * @var array Widget types
     */
    public $types = [
        self::TYPE_DEFAULT,
        self::TYPE_FULL_WIDTH_ACTIONS,
    ];

    public function init()
    {
        parent::init();

        $this->initMdlComponent();
        if (!isset($this->mdlOptions['toggleButtonOptions']['id'])) {
            $this->mdlOptions['toggleButtonOptions']['id'] = $this->getId() . 'ToggleButton';
        }
    }

    public function run()
    {
        // render Button
        if ($this->mdlOptions['toggleButtonID']) {
            $this->mdlOptions['toggleButtonOptions']['id'] = $this->mdlOptions['toggleButtonID'];
        } else {
            $buttonOptions = [
                'options' => $this->mdlOptions['toggleButtonOptions'],
                'mdlOptions' => $this->mdlOptions['toggleButtonMdlOptions'],
            ];
            $dialogButton = Button::widget($buttonOptions);
        }
        // render Dialog
        $header = Html::tag('h4', $this->mdlOptions['header'], ['class' => 'mdl-dialog__title']);
        $content = Html::tag('div', $this->mdlOptions['content'], ['class' => 'mdl-dialog__content']);
        $actions = '';

        foreach ($this->mdlOptions['actions'] as $action) {
            $actions .= Button::widget([
                'options' => (isset($action['options']) ? $action['options'] : []),
                'mdlOptions' => (isset($action['mdlOptions']) ? $action['mdlOptions'] : [])
            ]);
        }

        $actions .= Button::widget([
            'options' => [
                'class' => 'close'
            ],
            'mdlOptions' => [
                'label' => $this->mdlOptions['actionCancelText']
            ]
        ]);

        $actionClass = 'mdl-dialog__actions';
        if ($this->mdlOptions['type'] == self::TYPE_FULL_WIDTH_ACTIONS) {
            $actionClass .= ' mdl-dialog__actions--full-width';
        }
        $action = Html::tag('div', $actions, ['class' => $actionClass]);

        $dialog = Html::tag('dialog', $header . $content . $action, $this->options);
        $string = ((str_replace(["\n", "\r"], "", $dialog)));

        echo ($string);

        $this->registerDialogScript();
        $this->registerDialogStyle();
        if (isset($dialogButton)) {
            return $dialogButton;
        }
    }

    protected function registerDialogScript()
    {
        $js = new JsExpression('
            var dialog' . $this->options['id'] . ' = document.querySelector ("#' . $this->options['id'] . '");
            var showDialogButton' . $this->mdlOptions['toggleButtonOptions']['id'] . ' = document.querySelector ("#' . $this->mdlOptions['toggleButtonOptions']['id'] . '");
            if (!dialog' . $this->options['id'] . '.showModal){
                dialogPolyfill.registerDialog (dialog' . $this->options['id'] . ');
            }
            showDialogButton' . $this->mdlOptions['toggleButtonOptions']['id'] . '.addEventListener("click",function(){
                dialog' . $this->options['id'] . '.showModal();                
            });
            dialog' . $this->options['id'] . '.querySelector(".close").addEventListener("click",function() {
                dialog' . $this->options['id'] . '.close();
            });
        ');
        $this->view->registerJs($js);
    }

    protected function registerDialogStyle()
    {
        $css = '
            #' . $this->options['id'] . ' {
                width:auto;
                max-width:' . $this->mdlOptions['size'] . 'px;
            }
            @media (max-width:480px) {
                #' . $this->options['id'] . ' {
                    margin:auto 20px;
                }
            }
        ';
        $this->view->registerCss($css);
    }
}