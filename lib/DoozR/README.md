# Doozr's core classes and components
This part is the most important and interesting at the same time. Doozr's set of core classes and components is state-of-the-art and based on the most important OOP-patterns for a really good framework architecture. Doozr does not claim to be one fits all solution. This was never our goal and it wan't be that in the future. Doozr's main usecase is serving content with high speed while keeping the infrastructure high-scalable. Doozr takes control over parts which are often abstracted and outsourced. Continue reading to dig deeper into Doozr's architecture.

## Caching (#memcached)
Doozr instrumentalizes memcache (*php_memcached* extension and *memcached* daemon) to speedup itself. This step is somewhat logical that we can't go over this fact. Nothing is faster than accessing content from memory instead from network, filesystem or database (which is in fact: first network then filesystem access:). While developing *Doozr* we always profile and trace execution times, memory- and cpu-usage and identify the bottlenecks which can be UMGANGEN to give Doozr more speed. At the time of implementing this feature (over 2 years ago) there was a Zend-Framework Version out there which did not use any of these strategies. So Zend-Framework is building configs/merging and so on each request.
*Doozr* makes intensive use of mechanism like that.

## Request (CLI / WEB)
The Request class is available for both running modes CLI + WEB and available via the Front-controller:

    Doozr_Controller_Front->getRequest()

The request object provides access to all passed arguments and some important environment information like request-header (for running runtimeEnvironment *WEB*) and OS information (for running runtimeEnvironment *CLI*). For more information about the Request-Classes see: Request/README.me
