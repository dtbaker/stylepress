import {Route} from "react-router-dom"
import {config} from "../../util/config";

class Home {
  constructor() {
  }

  backendLoaded = () => {
  }

  backendRoute = () => {
    return <Route
      path="/"
      exact
      render={(props) => {
        return <div>Home</div>
      }}
    />
  }
}

export let home = new Home();
