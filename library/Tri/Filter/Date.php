<?php
class Tri_Filter_Date implements Zend_Filter_Interface
{
    /**
     * Set options
     * @var array
     */
    protected $_options = array(
        'locale'      => null,
        'date_format' => null,
        'precision'   => null
    );

    /**
     * Class constructor
     *
     * @param array|Zend_Config $options (Optional)
     */
    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (null === $options) {
            $locale = key(Zend_Registry::get('Zend_Locale')->getDefault());
            $date_format = Zend_Locale_Data::getContent($locale, 'date');

            $options = array('locale' => $locale, 'date_format' => $date_format);
        }

        $this->setOptions($options);
    }

    /**
     * Returns the set options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets options to use
     *
     * @param  array $options (Optional) Options to use
     * @return Tri_Filter_Date
     */
    public function setOptions(array $options = null)
    {
        $this->_options = $options + $this->_options;
        return $this;
    }

    /**
     * Filter date
     *
     * @param string $value
     * @return null|string
     */
    public function filter($value)
    {
        if ($value) {
            $date = new Zend_Date($value);
            if (Zend_Date::isDate($value, $this->_options['date_format'], $this->_options['locale'])) {
                return $date->toString('yyyy-MM-dd');
            } else {
                return $date->toString($this->_options['date_format']);
            }
        }
        return null;
    }
}
