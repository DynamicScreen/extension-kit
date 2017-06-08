<?php


namespace DynamicScreen\ExtensionKit;

use App\Models\Display;
use App\Models\Slide;

abstract class BaseSlideType
{
    private $slide_buffer = [];
    /**
     * @var ExtensionContract
     */
    protected $extension = null;

    protected $hidden = false;

    abstract public function getName();
    abstract public function fetchSlide(SlideContract $slide);

    public function getIdentifier()
    {
        return str_slug($this->getName());
    }

    public final function getFullIdentifier()
    {
        return $this->extension->getIdentifier() . '.' . $this->getIdentifier();
    }

    public function getDescription()
    {
        return '';
    }

    public function getIcon()
    {
        return 'square';
    }

    public function getDefaultOptions()
    {
        return [];
    }

    public function slideOptionsView()
    {
        return null;
    }

     public function processOptions($options)
     {
        return $options;
     }

    final protected function slide($data)
    {
        $this->slide_buffer[] = $data;
        return $this;
    }

    final public function flushSlides()
    {
        $slides = $this->slide_buffer;
        $this->slide_buffer = [];
        return $slides;
    }

    final public function toArray()
    {
        return [
            'full_identifier' => $this->getFullIdentifier(),
            'identifier' => $this->getIdentifier(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'extension' => $this->getExtension()->toArray(),
        ];
    }

	public function getSettings()
	{
		return [];
    }

    /**
     * @return ExtensionContract
     */
    public final function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param null $extension
     * @return BaseSlideType
     */
    public final function setExtension(ExtensionContract $extension)
    {
        $this->extension = $extension;
        return $this;
    }

    protected function registerOptionsForm(FormBuilder $form)
    {

    }

    public function getOptionsForm()
    {
        $form = new FormBuilder();
        $this->registerOptionsForm($form);
        return $form->getFields();
    }

    protected function processList($list)
    {
        return collect($list)->values()->toArray();
    }

    public function getValidations()
    {
        return [
            'rules' => [],
            'messages' => []
        ];
    }

    public function isHidden()
    {
        return $this->hidden;
    }

    public function isVisible()
    {
        return !$this->isHidden();
    }

    public function slides()
    {
        return Slide::where('type', $this->getFullIdentifier());
    }

}