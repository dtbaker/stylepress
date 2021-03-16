import { Application } from 'stimulus';
import { definitionsFromContext } from 'stimulus/webpack-helpers';
import './wizard.scss';

const application = Application.start();
const context = require.context( './', true, /\.js$/ );
application.load( definitionsFromContext( context ) );
