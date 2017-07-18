<?php
namespace Concrete\Package\MultipleExpressEntrySelector;

use Concrete\Core\Package\Package;

class Controller extends Package
{
    /**
     * @var string Package handle.
     */
    protected $pkgHandle = 'multiple_express_entry_selector';

    /**
     * @var string Required concrete5 version.
     */
    protected $appVersionRequired = '8.1.1';

    /**
     * @var string Package version.
     */
    protected $pkgVersion = '0.1';
    
    /**
     * @var array Array of location -> namespace autoloader entries for the package.
     */
    protected $pkgAutoloaderRegistries = [];
    
    protected $pkgAutoloaderMapCoreExtensions = true;
    
    /**
     * Returns the translated name of the package.
     *
     * @return string
     */
    public function getPackageName()
    {
        return t('Multiple Express Entry Selector');
    }

    /**
     * Returns the translated package description.
     *
     * @return string
     */
    public function getPackageDescription()
    {
        return t('Add a new attribute type to select one or more express entries');
    }

    public function install()
    {
        $pkg = parent::install();
        $factory = $this->app->make('Concrete\Core\Attribute\TypeFactory');
        $type = $factory->getByID('express_multiple');
        if (!is_object($type)) {
            $type = $factory->add('express_multiple', 'Express Multiple', $pkg);
            $service = $this->app->make('Concrete\Core\Attribute\Category\CategoryService');
            $category = $service->getByHandle('collection')->getController();
            $category->associateAttributeKeyType($type);
        }
    }
}
