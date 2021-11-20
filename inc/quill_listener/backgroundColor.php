<?php
    namespace nadar\quill\listener;

    use nadar\quill\Line;
    use nadar\quill\InlineListener;

    class BackgroundColor extends InlineListener
    {
        /**
         * @var boolean If ignore is enabled, the colors won't apply. This can be use full if coloring is disabled in your quill editor
         * but people copy past content from somewhere else which will then generate the color attribute.
         */
        public $ignore = false;

        /**
         * {@inheritDoc}
         */
        public function process(Line $line)
        {
            if (($color = $line->getAttribute('background'))) {
                $this->updateInput($line, $this->ignore ? $line->getInput() : '<span style="background:'.$line->getLexer()->escape($color).'">'.$line->getInput().'</span>');
            }
        }
    }
?>
