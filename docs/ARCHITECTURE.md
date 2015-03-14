<img src="https://avatars2.githubusercontent.com/u/514566?v=3&u=4615dfc4970d93dea5d3eaf996b7903ee6e24e20&s=140" align="right" />
---
![Logo of DoozR](logo-large.png)
The **lightweight** PHP-Framework for **high-performance** websites

This guide will explain you some of `DoozR's` architectural details and why we decided to implement some features like we did. Many features are completely data / or better measurement driven developed. We profile the complete Core while developing new features or improving performance. We make use of `uprofiler` for that like you can read in one of the following parts.


## Table of Contents

- [Profiling](#profiling)
- [MVP-Pattern](#mvp-pattern)
- [Install »](INSTALL.md)


## Profiling

uprofiler Lorem ipsum dolor set amet...


## MVP-Pattern

`DoozR` is build with plain PHP and provides a clean `MVP-Pattern` structure for your application. The implementation is based on a [Supervising Controller][1]. `DoozR` uses the observer-pattern to connect the View (observer) and Model (observer) through the Presenter (subject). View and Model (View and Model are both optional parts) getting attached to Presenter so that the Presenter operates encapsulated from View and Model and connect both by triggering events on which they react (observer).

[1]: https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter "MVP by Martin Fowler"
