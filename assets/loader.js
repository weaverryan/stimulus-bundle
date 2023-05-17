/**
 * Starts the Stimulus application and reads a map dump in the DOM to load controllers.
 *
 * Inspired by stimulus-loading.js from stimulus-rails.
 */
import { Application } from '@hotwired/stimulus';
import { eagerControllers, lazyControllers, isApplicationDebug } from './controllers.js';

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
    application.debug = isApplicationDebug;

    loadControllers(application);

    return application;
};
