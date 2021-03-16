import $ from 'jquery';
import { config } from './config';
import { error } from './error';

class API {
  constructor() {
    this.xhr = null;
    this.localCache = {};
  }

  post = ( endpoint, args, options ) => {
    const { abortExisting, cacheResults, ignoreErrors, retryCallback } = options;
    return new Promise( ( resolve, reject ) => {
      if ( ! args ) {
        args = {};
      }
      args._wpnonce = config.get( 'api_nonce' );
      if ( abortExisting && this.xhr ) {
        this.xhr.abort();
      }

      const cacheKey = endpoint + JSON.stringify( args );
      if ( cacheResults ) {
        if ( 'undefined' !== typeof this.localCache[cacheKey]) {
          resolve( this.localCache[cacheKey]);
        }
      }

      this.xhr = $.ajax({
        url: config.get( 'api_url' ) + endpoint,
        method: 'POST',
        dataType: 'json',
        data: args
      })
        .done( ( json ) => {
          if ( json && 'undefined' !== typeof json.status && ! json.status ) {
            json.error = true;
            if ( true !== ignoreErrors && 'undefined' !== typeof json.error_code ) {
              let errorMessage = json && json.error_message ? json.error_message : 'Unknown error';
              switch ( json.error_code ) {
                case 'token_extension_mismatch':
                  errorMessage = 'Please generate a new token, this one has already been used elsewhere.';
                  break;
                case 'invalid_token':
                  errorMessage = 'Sorry that is not a valid Envato Elements token';
                  break;
                case 'token_expired':
                  errorMessage = 'Sorry the token has expired, please generate a new one.';
                  break;
                case 'download_forbidden':
                  errorMessage = 'Sorry downloading this item is not allowed. Please confirm your Envato Elements Subscription is up to date.';
                  break;
                case 'item_not_found':
                  errorMessage = 'Sorry downloading this item is not allowed. The item may have been removed from Envato Elements.';
                  break;
                default:
                  switch ( json.error_status ) {
                    case 'http_request_failed':
                      errorMessage = `Envato Elements API connection failed. ${json.error_message}`;
                      break;
                    case 503:
                      errorMessage = 'Envato Elements API is temporarily down for maintenance, please try again soon.';
                      break;
                  }
              }
              error.displayError( 'API Error', errorMessage, json, false, retryCallback );
            }
          } else if ( cacheResults ) {
            this.localCache[cacheKey] = json;
          }
          resolve( json );
        })
        .fail( ( jqXHR, textStatus, errorThrown ) => {
          if ( jqXHR && 'abort' === jqXHR.statusText ) {
            reject({ aborted: true });
          } else {
            let response = {};
            try {
              response = JSON.parse( jqXHR.responseText );
            } catch ( e ) {}
            if ( 0 === Object.keys( response ).length ) {
              response = {
                message: 'Sorry something went wrong. ',
                debug: jqXHR.responseText
              };
            }
            if ( true !== ignoreErrors ) {
              const errorMessage = response && response.message ? response.message : 'Unknown error';
              let debugText = '';
              if ( jqXHR.responseText && 0 < jqXHR.responseText.length ) {
                debugText = jqXHR.responseText;
              } else if ( 0 < jqXHR.status ) {
                debugText =
                  'Empty API response. Please contact hosting provider and ensure WordPress memory limit is set to 128M or above.';
              } else {
                debugText =
                  'Sorry we had trouble communicating with the API, please check your internet connection, refresh the page and try again. ';
              }
              error.displayError( 'Temporary API Error', errorMessage, debugText, false, retryCallback );
            }
            reject( response );
          }
        })
        .always( () => {
          this.xhr = null;
        });
    });
  }
}

export const api = new API();
