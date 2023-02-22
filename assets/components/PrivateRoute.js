import React, {useContext} from 'react';
import {Route, Navigate, Outlet} from 'react-router-dom';
import {AuthContext} from '../Context/AuthContext';

class PrivateRoute extends Route {

    render() {
        const {component: Component, ...rest} = this.props;
        const {getToken} = useContext(AuthContext);

        return getToken() ? (
            <Route {...props} path={path}/>
        ) : (
            <React.Fragment>
                <Navigate to="/login" replace/>
            </React.Fragment>
        )
    }
}

export default PrivateRoute;

