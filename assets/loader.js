/**
 * Starts the Stimulus application and reads a map dump in the DOM to load controllers.
 *
 * Inspired by stimulus-loading.js from stimulus-rails.
 */
import { Application } from '@hotwired/stimulus';

// The following lines are dynamically rewritten by the asset mapper.
/** @type {Object<string, Controller>} */
export const eagerControllers = {};
/** @type {Object<string, string>} */
export const lazyControllers = {};

const controllerAttribute = 'data-controller';
const registeredControllers = {};

function registerController(name, controller, application) {
    if (!(name in registeredControllers)) {
        application.register(name, controller)
        registeredControllers[name] = true
    }
}

export const loadControllers = (application) => {
    // loop over the controllers map and require each controller
    for (const name in eagerControllers) {
        registerController(name, eagerControllers[name], application);
    }

    // TODO: add support for lazy controllers
};

export const startStimulusApp = () => {
    const application = Application.start();
    application.debug = true;

    loadControllers(application);

    return application;
};
