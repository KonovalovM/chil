import '../scss/main.scss';
import { init, ErrorBoundary } from '@sentry/react';
import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';

import { HashRouter as Router, Switch, Route } from 'react-router-dom';

import { LoadingStatus } from './Common/Types';
import { ErrorPage, LoadingPage, LoadingModal, ErrorModal, ErrorBoundaryFallback, BasketComponent } from './Components';

import { loadBasket } from './State/Basket';
import { loadUser } from './State/User';
import { RestaurantsPage } from './RestaurantsPage/RestaurantsPage';
import { MenuPage } from './MenuPage/MenuPage';
import { ProductPage } from './ProductPage/ProductPage';
import { AuthPage } from './AuthPage/AuthPage';
import { CheckoutPage } from './CheckoutPage/CheckoutPage';
import { ProfilePage } from './ProfilePage/ProfilePage';
import { OrderPage } from './OrderPage/OrderPage';

import Modal from 'react-modal';

Modal.setAppElement('body');

type AppLoading = { status: LoadingStatus.progress } | { status: LoadingStatus.success } | { status: LoadingStatus.error; error: Error };

function App() {
  const [loading, setLoading] = useState<AppLoading>({ status: LoadingStatus.progress });
  useEffect(
    () =>
      void (async () => {
        try {
          await loadBasket();
          await loadUser();
          setLoading({ status: LoadingStatus.success });
        } catch (error) {
          setLoading({ status: LoadingStatus.error, error });
        }
      })(),
    []
  );

  if (loading.status === LoadingStatus.progress) {
    return <LoadingPage />;
  }
  if (loading.status === LoadingStatus.error) {
    return (
      <Router>
        <ErrorPage error={loading.error} />
      </Router>
    );
  }

  return (
    <Router>
      <BasketComponent />
      <LoadingModal />
      <ErrorModal />
      <Switch>
        <Route exact path="/" component={RestaurantsPage} />
        <Route exact path="/:restaurantId/menu" component={MenuPage} />
        <Route path="/:restaurantId/products/:productId/:productBasketId?" component={ProductPage} />
        <Route exact path="/:restaurantId/checkout" component={CheckoutPage} />
        <Route exact path="/order/:orderId" component={OrderPage} />
        <Route path="/auth(/)*" component={AuthPage} />
        <Route path="/profile(/)*" component={ProfilePage} />
        <Route path="" component={() => <ErrorPage error={new Error("Page not found")}/>} />
      </Switch>
    </Router>
  );
}

init({dsn: SENTRY_DSN, environment: SENTRY_ENVIRONMENT, release: SENTRY_RELEASE});

ReactDOM.render(
  <ErrorBoundary fallback={ErrorBoundaryFallback}>
    <App />
  </ErrorBoundary>,
  document.getElementById('content')
);
