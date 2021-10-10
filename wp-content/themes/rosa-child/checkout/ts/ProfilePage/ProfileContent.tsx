import React, { useState, useCallback } from 'react';
import assign from 'lodash/assign';
import cloneDeep from 'lodash/cloneDeep';
import memoize from 'lodash/memoize';

import { LoadingStatus } from '../Common/Types';
import { LoadingPage, ErrorPage } from '../Components';
import { PhoneNumberInput } from '../Components/FormInputs/PhoneNumberInput';
import { getContactNumber, updateContactNumber, changePassword, updateUser } from '../State/User';
import { usePromise } from '../Utilities/Hooks';

import { OloUser } from '../OloAPI';

interface UserProps {
  firstname: string;
  lastname: string;
  emailaddress: string;
  contactNumber: string;
  currentPassword: string;
  newPassword: string;
  retypedPassword: string;
}

export const ProfileContent = ({ user }: { user: OloUser }) => {
  const contactNumberStatus = usePromise(async () => getContactNumber(), [user]);

  if (contactNumberStatus.status === LoadingStatus.progress) {
    return <LoadingPage />;
  }

  if (contactNumberStatus.status === LoadingStatus.error) {
    return <ErrorPage error={contactNumberStatus.error} />;
  }

  const userData: UserProps = {
    firstname: user.firstname,
    lastname: user.lastname,
    emailaddress: user.emailaddress,
    contactNumber: contactNumberStatus.value,
    currentPassword: '',
    newPassword: '',
    retypedPassword: '',
  };

  return <ProfileContentWithUserProps userData={userData} />;
};

const ProfileContentWithUserProps = (props: { userData: UserProps }) => {
  const oldUserData = props.userData;
  const [userData, setUserData] = useState<UserProps>(cloneDeep(oldUserData));
  const updateContactNumberHandler = useCallback((newContactNumber: string) => setUserData(assign(userData, { contactNumber: newContactNumber })), [
    userData,
    setUserData,
  ]);

  const [submitStatus, setSubmitStatus] = useState<LoadingStatus | null>(null);
  const [errorMessage, setErrorMessage] = useState<string>('');

  const handleInputChange = useCallback(
    memoize((attribute: string) => (e: React.ChangeEvent<HTMLInputElement>) => {
      setUserData({
        ...userData,
        [attribute]: e.currentTarget.value,
      });
    }),
    [userData, setUserData]
  );

  const handleOnSubmit = useCallback(
    async (event: React.FormEvent<HTMLFormElement>) => {
      event.preventDefault();

      setSubmitStatus(LoadingStatus.progress);
      try {
        await updateUserData(oldUserData, userData);
        setSubmitStatus(LoadingStatus.success);
      } catch (error) {
        setSubmitStatus(LoadingStatus.error);
        setErrorMessage(error.message);
      }
    },
    [oldUserData, userData, setSubmitStatus]
  );

  let statusText = null;
  if (submitStatus === LoadingStatus.progress) {
    statusText = <div>Loading...</div>;
  }
  if (submitStatus === LoadingStatus.error) {
    statusText = <div className="errorMessage">{'*' + errorMessage}</div>;
  }

  return (
    <div className="profileContent">
      <div className="header">
        <span>{(oldUserData.firstname.charAt(0) + oldUserData.lastname.charAt(0)).toUpperCase()}</span>
        <h1>{`Hi ${oldUserData.firstname}`}</h1>
      </div>

      <form onSubmit={handleOnSubmit}>
        <input type="text" name="firstname" placeholder="Name" value={userData.firstname} onChange={handleInputChange('firstname')} required />
        <input type="text" name="lastname" placeholder="Last Name" value={userData.lastname} onChange={handleInputChange('lastname')} required />
        <input
          type="email"
          name="emailaddress"
          placeholder="Email Address"
          value={userData.emailaddress}
          onChange={handleInputChange('emailaddress')}
          required
        />
        <PhoneNumberInput phoneNumber={userData.contactNumber} onChange={updateContactNumberHandler} required/>

        <h2>NEW PASSWORD</h2>
        <input
          type="password"
          name="currentPassword"
          placeholder="Current Password"
          value={userData.currentPassword}
          onChange={handleInputChange('currentPassword')}
        />
        <input type="password" name="newPassword" placeholder="New Password" value={userData.newPassword} onChange={handleInputChange('newPassword')} />
        <input
          type="password"
          name="retypedPassword"
          placeholder="Retype New Password"
          value={userData.retypedPassword}
          onChange={handleInputChange('retypedPassword')}
        />

        {statusText}

        <input type="submit" value="UPDATE DETAILS" />
      </form>
    </div>
  );
};

const updateUserData = async (oldUserData: UserProps, newUserData: UserProps) => {
  if (
    oldUserData.firstname !== newUserData.firstname ||
    oldUserData.lastname !== newUserData.lastname ||
    oldUserData.emailaddress !== newUserData.emailaddress
  ) {
    await updateUser(newUserData.firstname, newUserData.lastname, newUserData.emailaddress);
  }

  if (oldUserData.contactNumber !== newUserData.contactNumber) {
    await updateContactNumber(newUserData.contactNumber);
  }

  if (newUserData.currentPassword !== '' && newUserData.newPassword !== '' && newUserData.retypedPassword !== '') {
    if (newUserData.newPassword === newUserData.retypedPassword) {
      await changePassword(newUserData.currentPassword, newUserData.newPassword);
    } else {
      throw new Error('The passwords do not match');
    }
  }
};
