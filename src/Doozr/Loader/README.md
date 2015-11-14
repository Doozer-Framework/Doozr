# Loader
## Which types of loaders does *Doozr* provide?
*Doozr* does follow the **SP(1|2)**-Standards as far as possible! So we implemented our loading mechanism on top of *SPL* of *PHP* and by following the current existing standards. Cause the standard is still in it's beta-phase we can't garantee for any upcoming incompatibilities.


## Serviceloader
The **Serviceloader** is an elementary part of *Doozr's* structure. All Services are loaded easily with no pain at all with the provided *Doozr\_Loader\_Serviceloader*. No matter if you want to load a module from your own namespace (e.g. Foo) or from Doozr's default namespace (Doozr). It's just one simple method call like you can see here:

    Doozr_Loader_Serviceloader::load($servicename, $namespace [optional]);
  
The Serviceloader is build on top of Doozr's SPL abstraction layer. We follow one strict policy within the Doozr development team "prevent use of autoloading for core elements". But on the other hand we want also provide a mechanism for developer to build modules which can rely on Doozr's prebuild autoloading mechanisms without having them to build their own. More about the SPL facade of Doozr under "SPL Abstraction".

## SPL Abstraction
*Doozr* provides you a high-level SPL abstraction layer in form of a facade which enables you to easily switch the priority of an autoloader, add or update an autoloader and so many more.

    Doozr_Loader_Autoloader_Spl_Facade
    Doozr_Loader_Autoloader_Spl_Config
    
Those both classes replace the currently still existing *Doozr\_Loader\_Autoloader*.

## Autoloader (@deprecated)
Do not use *Doozr\_Loader\_Autoloader*.
