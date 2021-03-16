import { api } from './api';
import { error } from './error';

class Config {
  constructor() {
    this.config = {};
    this.stateData = {};
  }

  set = ( config ) => {
    this.config = Object.assign({}, this.config, config );
  }

  persist = ( key, value ) => {
    return new Promise( ( resolve, reject ) => {
      api
        .post(
          'options/set',
          {
            key,
            value
          },
          { abortExisting: true, ignoreErrors: true },
        )
        .then(
          ( json ) => {
            if ( json ) {
              if ( 'undefined' !== typeof json.config ) {
                this.set( json.config );
              }
              resolve( json );
            } else {
              reject();
            }
          },
          ( err ) => {
            reject( err );
          },
        )
        .finally( () => {});
    });
  }

  get = ( key ) => {
    return 'undefined' !== typeof this.config[key] ? this.config[key] : false;
  }

  state = ( key, value ) => {
    if ( 'undefined' !== typeof value ) {
      this.stateData[key] = value;
      return value;
    }
    return 'undefined' !== typeof this.stateData[key] ? this.stateData[key] : false;
  }

  shouldWeShowPremiumContent = ( searchQuery ) => {
    return ( searchQuery.premium && 'show' === searchQuery.premium ) || ! searchQuery.premium;
  }

  shouldWeShowElementorProContent = ( searchQuery ) => {
    return ( searchQuery.elementor && 'pro' === searchQuery.elementor ) || ! searchQuery.elementor;
  }
}

export const config = new Config();
