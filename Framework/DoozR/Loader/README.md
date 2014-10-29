# Loader
## Which types of loaders does *DoozR* provide?
*DoozR* does follow the **SP(1|2)**-Standards as far as possible! So we implemented our loading mechanism on top of *SPL* of *PHP* and by following the current existing standards. Cause the standard is still in it's beta-phase we can't garantee for any upcoming incompatibilities.


## Serviceloader
The **Serviceloader** is an elementary part of *DoozR's* structure. All Services are loaded easily with no pain at all with the provided *DoozR\_Loader\_Serviceloader*. No matter if you want to load a module from your own namespace (e.g. Foo) or from DoozR's default namespace (DoozR). It's just one simple method call like you can see here:

    DoozR_Loader_Serviceloader::load($servicename, $namespace [optional]);
  
The Serviceloader is build on top of DoozR's SPL abstraction layer. We follow one strict policy within the DoozR development team "prevent use of autoloading for core elements". But on the other hand we want also provide a mechanism for developer to build modules which can rely on DoozR's prebuild autoloading mechanisms without having them to build their own. More about the SPL facade of DoozR under "SPL Abstraction".

## SPL Abstraction
*DoozR* provides you a high-level SPL abstraction layer in form of a facade which enables you to easily switch the priority of an autoloader, add or update an autoloader and so many more.

    DoozR_Loader_Autoloader_Spl_Facade
    DoozR_Loader_Autoloader_Spl_Config
    
Those both classes replace the currently still existing *DoozR\_Loader\_Autoloader*.

## Autoloader (@deprecated)
Do not use *DoozR\_Loader\_Autoloader*.
