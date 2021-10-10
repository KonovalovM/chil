import memoize from 'lodash/memoize';
import React, { useState, useCallback } from 'react';
import { useHistory } from 'react-router-dom';
import { createGuestUser } from '../State/User';
import { getBasketData } from '../State/Basket';

export function AuthSelectorContent({ redirect } : { redirect: () => void }) {

	const [errorMessage, setErrorMessage] = useState<string>('');
	const history = useHistory();

	const signInAsGuess = useCallback(() => {
		const basket = getBasketData();
		if(!basket || basket.products.length == 0) {
			setErrorMessage('You can only sign in as guest if you have items in the basket');
			return;
		}

		try {
			createGuestUser();
			redirect();
		}
		catch(error) {
			setErrorMessage(error.message);
		}
	}, [redirect, setErrorMessage]);

	const goToPage = useCallback(memoize((url: string) => () => history.push(url)), [history]);

	return (
		<div className="formContainer">
			<div className="header">
        <h1>How would you like to proceed?</h1>
      </div>

      {errorMessage ? <div className="errorMessage">{'*' + errorMessage}</div> : null}

      <div className="authSelectorOptions">
	      <button onClick={goToPage("/auth/sign-in")}>
	      	sign in
	      </button>
	      <button onClick={signInAsGuess}>
	        check out as a guest
	      </button>
	      <button onClick={goToPage("/auth/sign-up")}>
	      	sign up
	      </button>
      </div>
		</div>
	);
}