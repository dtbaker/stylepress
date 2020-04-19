import $ from "jquery"
import React, { Component } from "react"
import ReactDOM from "react-dom"
import styles from "./modal.module.css"
import { config } from "./config"

class ModalPopup extends Component {
  constructor(props) {
    super(props)
    this.state = {
      showDebugDetails: false,
      modalOpen: false,
      modalData: {},
    }
  }

  componentDidMount() {
    /* $("body").on("click", (event) => {
      const { modalOpen } = this.state
      if (modalOpen && !$(event.target).parents(`.${styles.inner}`).length) {
        this.closeModal()
      }
    }) */
  }

  componentDidUpdate(prevProps, prevState, snapshot) {
    const { modalData, modalOpen } = this.state
    if (modalOpen) {
      if (typeof modalData.message !== "string") {
        this.modalBody && this.modalBody.appendChild(modalData.message)
      }
    }
  }

  closeModal = () => {
    this.setState({ modalOpen: false })
    $("body").removeClass("envato-elements--modal-open")
  }

  openModal = (templateData) => {
    this.setState({
      modalOpen: true,
      modalData: templateData,
    })
    $("body").addClass("envato-elements--modal-open")
  }

  render() {
    const { modalData, modalOpen, showDebugDetails } = this.state
    return (
      <div data-elements-modal="yes" className={`${styles.wrap} ${modalOpen ? styles.open : styles.closed}`}>
        {modalOpen ? (
          <div className={styles.inner}>
            <div className={styles.content}>
              <div className={styles.headerRow}>
                <h3 className={styles.title}>{modalData.title}</h3>
                <button className={styles.closeButton} onClick={modalData.closeModal || this.closeModal}>
                  Close
                </button>
              </div>
              <div className={styles.body} ref={(modalBody) => (this.modalBody = modalBody)}>
                {typeof modalData.message !== "object" ? modalData.message : ""}
              </div>
              {typeof modalData.tryAgain === "function" || typeof modalData.debug !== "undefined" ? (
                <div className={styles.debugActions}>
                  {typeof modalData.tryAgain === "function" ? (
                    <button
                      className={styles.buttonRetry}
                      onClick={() => {
                        this.closeModal()
                        modalData.tryAgain()
                      }}>
                      Try Again
                    </button>
                  ) : null}
                  <button
                    className={styles.buttonRefresh}
                    onClick={(e) => {
                      e.preventDefault()
                      window.location.reload()
                      return false
                    }}>
                    Refresh Page
                  </button>
                  {typeof modalData.debug !== "undefined" ? (
                    <button
                      className={styles.buttonDebug}
                      onClick={() => {
                        this.setState({ showDebugDetails: !showDebugDetails })
                      }}>
                      {showDebugDetails ? `Hide` : `Show`} Debug Details
                    </button>
                  ) : null}
                  <a href={config.get("license_deactivate")} className={styles.buttonRefresh}>
                    Reset Plugin Settings
                  </a>
                </div>
              ) : null}
              {showDebugDetails && typeof modalData.debug !== "undefined" ? (
                <div className={styles.debug}>
                  <textarea
                    className={styles.debugText}
                    onClick={(e) => {
                      e.target.focus()
                      e.target.select()
                    }}
                    defaultValue={
                      modalData.debug && modalData.debug.debug
                        ? modalData.debug.debug
                        : typeof modalData.debug === "object"
                        ? JSON.stringify(modalData.debug)
                        : modalData.debug
                    }
                  />
                </div>
              ) : (
                ""
              )}
              <div className={styles.footer}>
                {/* <span>[put email address here]</span> */}
                {modalData.reactivate ? <span>[put a link to reactivate plugin here]</span> : ""}
              </div>
            </div>
          </div>
        ) : (
          ""
        )}
      </div>
    )
  }
}

class Modal {
  constructor() {
    this.$modalDom = null
  }

  init = () => {
    this.$modalDom = document.createElement("div")
    document.body.appendChild(this.$modalDom)
    ReactDOM.render(
      <ModalPopup
        ref={(modalComponent) => {
          this.modalComponent = modalComponent
        }}
      />,
      this.$modalDom,
    )
  }

  closeModal = () => {
    this.modalComponent.closeModal()
  }

  openModal = (templateData) => {
    this.modalComponent.openModal(templateData)
  }
}

export const modal = new Modal()
