import assign from 'lodash/assign';
import memoize from 'lodash/memoize';
import React, { useState, useCallback } from 'react';
import { useHistory } from 'react-router-dom';
import { register } from '../State/User';
import { PhoneNumberInput } from '../Components/FormInputs/PhoneNumberInput';

interface NewUser {
  firstname: string;
  lastname: string;
  emailaddress: string;
  contactnumber: string;
  password: string;
  retypedPassword: string;
}

export function SignUpContent({ redirect }: { redirect: () => void }) {
  const [newUserData, setNewUserData] = useState<NewUser>({
    firstname: '',
    lastname: '',
    emailaddress: '',
    contactnumber: '',
    password: '',
    retypedPassword: '',
  });

  const updateContactNumber = useCallback((newContactNumber: string) => setNewUserData(assign(newUserData, { contactnumber: newContactNumber })), [
    newUserData,
    setNewUserData,
  ]);
  const [errorMessage, setErrorMessage] = useState<string>('');

  const handleInputChange = useCallback(
    memoize((attribute: string) => (e: React.ChangeEvent<HTMLInputElement>) => {
      setNewUserData({
        ...newUserData,
        [attribute]: e.currentTarget.value,
      });
    }),
    [newUserData, setNewUserData]
  );

  const handleOnSubmit = useCallback(
    async (event: React.FormEvent<HTMLFormElement>) => {
      event.preventDefault();

      const { firstname, lastname, emailaddress, contactnumber, password, retypedPassword } = newUserData;

      if (password !== retypedPassword) {
        setErrorMessage('The passwords do not match');
        return;
      }
      if (contactnumber.length !== 10) {
        setErrorMessage('The phone number should have 10 digits');
        return;
      }

      try {
        await register(firstname, lastname, emailaddress, password, contactnumber);
        redirect();
      } catch (error) {
        setErrorMessage(error.message);
      }
    },
    [newUserData, setErrorMessage, redirect, setErrorMessage]
  );

  const history = useHistory();

  const goBack = useCallback(()=> {
    history.goBack();
  }, [history]);

  return (
    <>
      <div className="formContainer">
        <div className="header">
          <h1>Create an Account</h1>
        </div>
        <form onSubmit={handleOnSubmit}>
          <input type="text" placeholder="First Name" name="firstname" onChange={handleInputChange('firstname')} maxLength={36} required />
          <input type="text" placeholder="Last Name" name="lastname" onChange={handleInputChange('lastname')} maxLength={36} required />
          <input type="email" name="emailaddress" placeholder="Email Address" onChange={handleInputChange('emailaddress')} maxLength={128} required />
          <input type="password" name="password" placeholder="Password" onChange={handleInputChange('password')} minLength={7} maxLength={1024} required />
          <input
            type="password"
            name="retypedPassword"
            placeholder="Retype Password"
            onChange={handleInputChange('retypedPassword')}
            minLength={7}
            maxLength={1024}
            required
          />
          <PhoneNumberInput phoneNumber={newUserData.contactnumber} onChange={updateContactNumber} required />

          {errorMessage ? <div className="errorMessage">{'*' + errorMessage}</div> : null}

          <input type="submit" value="CREATE AN ACCOUNT" />
          <input type="button" onClick={goBack} value="GO BACK" />
        </form>
      </div>
      <div className="terms">
        By creating an account, you agree to our
        <a href="#">Terms &amp; Conditions and Reward Terms &amp; Conditions</a>
      </div>
    </>
  );
}
