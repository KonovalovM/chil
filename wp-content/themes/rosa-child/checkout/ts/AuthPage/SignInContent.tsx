import React, { useCallback, useState } from 'react';
import { Link } from 'react-router-dom';
import { login } from '../State/User';
import { getBasketData } from '../State/Basket';

export function SignInContent({
  redirect,
}: {
  redirect: () => void;
}) {
  const [email, setEmail] = useState<string>('');
  const [password, setPassword] = useState<string>('');

  const [errorMessage, setErrorMessage] = useState<string>('');

  const handleEmailChange = useCallback(
    (event: React.ChangeEvent<HTMLInputElement>) => {
      setEmail(event.currentTarget.value);
    },
    [setEmail]
  );

  const handlePasswordChange = useCallback(
    (event: React.ChangeEvent<HTMLInputElement>) => {
      setPassword(event.currentTarget.value);
    },
    [setPassword]
  );

  const handleOnSubmit = useCallback(
    async (event: React.FormEvent<HTMLFormElement>) => {
      event.preventDefault();
      try {
        const basket = getBasketData();
        const basketId = basket ? basket.id : undefined;
        await login(email, password, basketId);
        redirect();
      } catch (error) {
        setErrorMessage(error.message);
      }
    },
    [email, password, redirect, setErrorMessage]
  );

  return (
    <div className="formContainer">
      <div className="header">
        <h1>Welcome back!</h1>
      </div>
      <h2>Sign In</h2>
      {errorMessage ? <div className="errorMessage">{'*' + errorMessage}</div> : null}
      <form onSubmit={handleOnSubmit}>
        <input type="email" name="email" placeholder="Email Address" onChange={handleEmailChange} required />
        <input type="password" name="password" placeholder="Password" onChange={handlePasswordChange} required />
        <input type="submit" value="SIGN IN" />
      </form>
      <div className="forgotPasswordOption">
        <Link className="link" to="/auth/forgot-password">Forgot your password?</Link>
      </div>
      <div className="signUpOption">
        <p>New to Chilantro App?</p>
        <Link className="link" to="/auth/sign-up">Sign Up</Link>
      </div>
    </div>
  );
}
