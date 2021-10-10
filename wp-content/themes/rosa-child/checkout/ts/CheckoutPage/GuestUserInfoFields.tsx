import memoize from 'lodash/memoize';
import assign from 'lodash/assign'
import React, { useState, useEffect, useCallback } from 'react';
import { OloUser } from '../OloAPI';
import { setUserProperties } from '../State/User';
import { PhoneNumberInput } from '../Components/FormInputs/PhoneNumberInput';

export function GuestUserInfoFields({ user, showValidationErrors }: { user: OloUser, showValidationErrors: boolean }) {

	const [userData, setUserData] = useState<{ [attribute: string] : string }>(user.type === "guest" ? user : {
		firstname: "",
		lastname: "",
		emailaddress: "",
		contactnumber: "",
		type: "guest"
	});

	const handleInputChange = useCallback(
    memoize((attribute: string) => (e: React.ChangeEvent<HTMLInputElement>) => {
      const newUserData = assign({}, userData, { [attribute]: e.currentTarget.value });
      setUserData(newUserData);
    }),
    [userData, setUserData]
  );

	const updateContactNumberHandler = useCallback((contactnumber: string) => {
		const newUserData = assign({}, userData, {'contactnumber': contactnumber});
		setUserData(newUserData);
	}, [userData, setUserData]);


	useEffect(() => {
		setUserProperties(userData);
	}, [userData]);

	return (
		<div className="guestUserInfoFields">
			{showValidationErrors && !checkValue(userData, "firstname") ? <div className="errorMessage">*This field is required.</div> : null }
			<input type="text" placeholder="First Name" name="firstname" onChange={handleInputChange('firstname')} maxLength={36} />

			{showValidationErrors && !checkValue(userData, "lastname") ? <div className="errorMessage">*This field is required.</div> : null }
      <input type="text" placeholder="Last Name" name="lastname" onChange={handleInputChange('lastname')} maxLength={36} />

      {showValidationErrors && !checkValue(userData, "emailaddress") ? <div className="errorMessage">*This field is required.</div> : null }
      <input type="email" placeholder="Email Address" name="emailaddress" onChange={handleInputChange('emailaddress')} maxLength={128} />

      {showValidationErrors && !checkValue(userData, "contactnumber") ? <div className="errorMessage">*This field is required.</div> : null }
      <PhoneNumberInput phoneNumber={userData.contactnumber} onChange={updateContactNumberHandler} required={false}/>
		</div>
	);
}

function checkValue(object: any, attribute: string) {
	if(object[attribute] === undefined || object[attribute] === "") {
		return false;
	}
	return true;
}