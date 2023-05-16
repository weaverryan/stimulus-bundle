/**
 * Starts the Stimulus application and reads a map dump in the DOM to load controllers.
 *
 * Inspired by stimulus-loading.js from stimulus-rails.
 */
import { Application } from '@hotwired/stimulus';
import { eagerControllers, lazyControllers, isApplicationDebug } from './controllers.js';

const controllerAttribute = 'data-controller';

export const loadControllers = (application) => {
    // loop over the controllers map and require each controller
    for (const name in eagerControllers) {
        registerController(name, eagerControllers[name], application);
    }

    loadLazyControllers(application);
};

export const startStimulusApp = () => {
    const application = Application.start();
    application.debug = isApplicationDebug;

    loadControllers(application);

    return application;
};

function registerController(name, controller, application) {
    if (canRegisterController(name, application)) {
        application.register(name, controller)
    }
}

function loadLazyControllers(application) {
    lazyLoadExistingControllers(application, document);
    lazyLoadNewControllers(application, document)
}

function lazyLoadExistingControllers(application, element) {
  queryControllerNamesWithin(element).forEach(controllerName => loadController(controllerName, application))
}
function queryControllerNamesWithin(element) {
  return Array.from(element.querySelectorAll(`[${controllerAttribute}]`)).map(extractControllerNamesFrom).flat()
}
function extractControllerNamesFrom(element) {
  return element.getAttribute(controllerAttribute).split(/\s+/).filter(content => content.length)
}
function lazyLoadNewControllers(application, element) {
  new MutationObserver((mutationsList) => {
    for (const { attributeName, target, type } of mutationsList) {
      switch (type) {
        case 'attributes': {
          if (attributeName === controllerAttribute && target.getAttribute(controllerAttribute)) {
            extractControllerNamesFrom(target).forEach(controllerName => loadController(controllerName, application))
          }
        }

        case 'childList': {
          lazyLoadExistingControllers(application, target)
        }
      }
    }
  }).observe(element, { attributeFilter: [controllerAttribute], subtree: true, childList: true })
}
function canRegisterController(name, application){
  return !application.router.modulesByIdentifier.has(name)
}

async function loadController(name, application) {
    if (canRegisterController(name, application)) {
        if (lazyControllers[name] === undefined) {
            console.error(`Failed to autoload controller: ${name}`);
        }

        const controllerModule = await (lazyControllers[name]());

        registerController(name, controllerModule.default, application);
    }
}
