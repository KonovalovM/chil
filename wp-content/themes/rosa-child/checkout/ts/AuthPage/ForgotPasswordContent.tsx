import React, { useState, useCallback } from 'react';
import { useHistory } from 'react-router-dom';
import { resetUserPassword } from '../OloAPI';

export function ForgotPasswordContent({
  redirect,
}: {
  redirect: () => void;
}) {
  const [email, setEmail] = useState<string>('');
  const [submitted, setSubmitted] = useState<boolean>(false);
  const [errorMessage, setErrorMessage] = useState<string>('');

  const emailChangeHandler = useCallback((event: React.ChangeEvent<HTMLInputElement>) => setEmail(event.currentTarget.value), [setEmail]);

  const handleOnSubmit = useCallback(
    async (event: React.FormEvent<HTMLFormElement>) => {
      event.preventDefault();
      try {
        await resetUserPassword(email);
        setSubmitted(true);
      } catch (error) {
        setErrorMessage(error.message);
        setSubmitted(false);
      }
    },
    [email, setSubmitted, setErrorMessage]
  );

  const history = useHistory();

  const goBack = useCallback(()=> {
    history.goBack();
  }, [history]);

  return (
    <div className="formContainer">
      <div className="header">
        <h1>Reset Your Password</h1>
      </div>
      {errorMessage ? <div className="errorMessage">{'*' + errorMessage}</div> : null}
      <p>{submitted ? `We send an email to ${email}` : 'Enter your email below, and we\'ll send you an email that will tell you what to do next'}</p>
      {!submitted ? (
        <form onSubmit={handleOnSubmit}>
          <input type="email" name="email" placeholder="Email Address" onChange={emailChangeHandler} required />

          <input type="submit" value="CONTINUE" />
          <input type="button" onClick={goBack} value="GO BACK" />
        </form>
      ) : (
        <input type="button" onClick={goBack} value="GO BACK" />
      )}
    </div>
  );
}
