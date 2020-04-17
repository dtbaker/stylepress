import { api } from "./api"
import { error } from "./error"

class Config {
  constructor() {
    this.config = {}
    this.stateData = {}
  }

  set = (config) => {
    this.config = Object.assign({}, this.config, config)
  }

  persist = (key, value) => {
    return new Promise((resolve, reject) => {
      api
        .post(
          "options/set",
          {
            key,
            value,
          },
          { abortExisting: true, ignoreErrors: true },
        )
        .then(
          (json) => {
            if (json) {
              if (typeof json.config !== "undefined") {
                this.set(json.config)
              }
              resolve(json)
            } else {
              reject()
            }
          },
          (err) => {
            reject(err)
          },
        )
        .finally(() => {})
    })
  }

  get = (key) => {
    return typeof this.config[key] !== "undefined" ? this.config[key] : false
  }

  state = (key, value) => {
    if (typeof value !== "undefined") {
      this.stateData[key] = value
      return value
    }
    return typeof this.stateData[key] !== "undefined" ? this.stateData[key] : false
  }

  shouldWeShowPremiumContent = (searchQuery) => {
    return (searchQuery.premium && searchQuery.premium === `show`) || !searchQuery.premium
  }

  shouldWeShowElementorProContent = (searchQuery) => {
    return (searchQuery.elementor && searchQuery.elementor === `pro`) || !searchQuery.elementor
  }
}

export const config = new Config()
