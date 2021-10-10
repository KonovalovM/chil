import find from 'lodash/find';
import trimEnd from 'lodash/trimEnd';
import React, { useCallback, useState, useEffect } from 'react';
import { useHistory, useLocation } from 'react-router-dom';
import memoize from 'memoizee';

import { isUserAuthenticated } from '../State/User';
import { setRedirectUrl, getRedirectUrl, setErrorModalMessage } from '../State/Global';
import { NavigationBar, ErrorPage } from '../Components';
import { AuthSelectorContent } from './AuthSelectorContent';
import { SignInContent } from './SignInContent';
import { ForgotPasswordContent } from './ForgotPasswordContent';
import { SignUpContent } from './SignUpContent';

export function AuthPage() {
  const pagesData = [
    { url: '/auth', component: AuthSelectorContent, defaultRedirectUrl: '/' },
    { url: '/auth/sign-in', component: SignInContent, defaultRedirectUrl: '/profile' },
    { url: '/auth/sign-up', component: SignUpContent, defaultRedirectUrl: '/profile' },
    { url: '/auth/forgot-password', component: ForgotPasswordContent }
  ];

  const history = useHistory();
  const { pathname } = useLocation();

  const redirect = useCallback(() => {
    const savedRedirectUrl = getRedirectUrl();
    setRedirectUrl(undefined);

    const currentPage = find(pagesData, { url: trimEnd(pathname, '/') })!;
    const defaultRedirectUrl = currentPage.defaultRedirectUrl;

    const redirectUrl = savedRedirectUrl || defaultRedirectUrl;

    if(!redirectUrl) {
      setErrorModalMessage(new Error("Unexpected error: redirect url not defined"));
      return;
    }

    history.push(redirectUrl);
  }, [history, pathname]);

  //if the user is authenticated, redirect
  useEffect(() => (isUserAuthenticated() ? redirect() : undefined), [redirect]);

  useEffect(() => window.scrollTo(0, 0), [pathname]);

  const currentPage = find(pagesData, { url: trimEnd(pathname, '/') });

  if (!currentPage) {
    return <ErrorPage error={new Error("Page not found")} />;
  }

  const ContentComponent = currentPage.component;

  return (
    <div className="authPage">
      <NavigationBar />
      <div className="authPageContainer">
        <div className="background">
          <div className="overlay">
            <ContentComponent redirect={redirect}/>
          </div>
        </div>
      </div>
    </div>
  );
}
