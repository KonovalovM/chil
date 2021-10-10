import React, { useState, useEffect } from 'react';
import { observable, action } from 'mobx';
import { observer } from 'mobx-react';
import { useHistory, useLocation } from 'react-router';

import assign from 'lodash/assign';
import { loadValue, saveValue, removeValue } from './localStorage';
import { LoadingStatus } from '../Common/Types';
import { LoadingPage, ErrorPage } from '../Components';
import { ObservableObjectType } from '../Utilities/componentHelper';
import { setRedirectUrl } from './Global';

import {
  OloUser,
  getUser,
  getContactNumber as OloGetContactNumber,
  changePassword as OloChangePassword,
  updateUser as OloUpdateUser,
  authenticateUser,
  logoutUser,
  createUser,
  getDeliveryAddresses as OloGetDeliveryAddresses,
  deleteDeliveryAddress as OloDeleteDeliveryAddress,
  getUserBillingAccounts as OloGetUserBillingAccounts,
  updateContactNumber as OloUpdateContactNumber,
  deleteUserBillingAccount as OloDeleteUserBillingAccount,
  getRecentOrders as OloGetRecentOrders
} from '../OloAPI';

/* --------------------------------------------------------------------------*/
/* State                                                                     */
/* --------------------------------------------------------------------------*/

export type UserState =
  | {
      status: LoadingStatus.progress;
    }
  | {
      error: Error;
      status: LoadingStatus.error;
    }
  | {
      data: OloUser | undefined;
      status: LoadingStatus.success;
    };

const userState = observable({
  data: undefined,
  status: LoadingStatus.progress,
} as UserState);

/* --------------------------------------------------------------------------*/
/* Helper Functions                                                          */
/* --------------------------------------------------------------------------*/

const setUserError = action((error: Error) => {
  assign(userState, { error, status: LoadingStatus.error });
});

const setUserState = action((newUser: OloUser | undefined) => {
  assign(userState, { data: newUser, status: LoadingStatus.success });
  if(newUser === undefined) {
    removeValue('userData');
    return;
  }

  const userData = newUser.type === 'user' ? { authtoken: newUser.authtoken } : { guest: 'true' };
  saveValue('userData', JSON.stringify(userData));
});

export const setUserProperties =action((properties: any) => {
  const user = requireUserData();
  assign(user, properties);
});

const requireUserData = () => {
  if (userState.status !== LoadingStatus.success || !userState.data) {
    throw new Error('Unable to access user: User is not logged in');
  }
  return userState.data;
};

export const requireAuthtoken = () => {
  const user = requireUserData();
  if(user.type === 'user') {
    return user.authtoken;
  }

  throw new Error("Unexpected error: the authtoken is not accessible to a guest user");
}

export const getAuthtoken = () => {
  const user = userState.status === LoadingStatus.success ? userState.data : undefined;
  if(!user || user.type === 'guest') {
    return undefined;
  }

  return user.authtoken;
}

function handleUserErrors<Arguments extends unknown[], ReturnType>(
  operation: (...args: Arguments) => Promise<ReturnType>
): (...args: Arguments) => Promise<ReturnType> {
  return async (...args: Arguments) => {
    try {
      return await operation(...args);
    } catch (error) {
      setUserError(error);
      throw error;
    }
  };
}

/* --------------------------------------------------------------------------*/
/* State Interaction Functions                                               */
/* --------------------------------------------------------------------------*/

export function withUserState<PropType extends { userState: UserState }>(
  WrappedComponent: React.FunctionComponent<PropType>
): React.FunctionComponent<Omit<PropType, 'userState'>> {
  return (props: Omit<PropType, 'userState'>) => {
    return <WrappedComponent {...(props as PropType)} userState={userState} />;
  };
}

const makeWithAuthUser = <PropName extends string, Data>(propName: PropName, observableObject: ObservableObjectType<Data>, authCallback: () => boolean, redirectUrl: string) => {
  return <PropType extends { [key in PropName]: Data }>(
    WrappedComponent: React.FunctionComponent<PropType>
  ): React.FunctionComponent<Omit<PropType, PropName>> => {
    const ObserverComponent = observer((props: PropType & { observableObject: ObservableObjectType<Data> }) => {
      const history = useHistory();
      const location = useLocation();

      const [content, setContent] = useState<React.ReactElement|null>(null);

      useEffect(() => {
        if(props.observableObject.status == LoadingStatus.progress) {
          setContent(<LoadingPage />);
        }
        else if(props.observableObject.status === LoadingStatus.error) {
          setContent(<ErrorPage error={props.observableObject.error} />);
        }
        else if(authCallback()) {
          setContent(<WrappedComponent {...(props as PropType)} {...{ [propName]: props.observableObject.data }} />);
        }
        else {
          //if the user is not isUserAuthenticated
          //redirects to redirectUrl
          setContent(<ErrorPage error={new Error('Unexpected: invalid user state')} />);
          setRedirectUrl(location.pathname);
          history.push(redirectUrl);
        }
      }, [props, setContent]);

      return content;
    });
    return (props: Omit<PropType, PropName>) => {
      return <ObserverComponent {...(props as PropType)} observableObject={observableObject} />;
    };
  };
};

export const withAuthUser = makeWithAuthUser('user', userState, isUserAuthenticated, '/auth/sign-in');
export const withUser = makeWithAuthUser('user', userState, isUserDefined, '/auth');

export const loadUser = handleUserErrors(async () => {
  const userDataString = loadValue('userData');
  if(!userDataString) {
    return setUserState(undefined);
  }

  const userData = JSON.parse(userDataString);
  const authtoken = userData.authtoken;
  const guest = userData.guest;

  if(guest) {
    return createGuestUser();
  }

  if(authtoken) {
    try {
      setUserState(await getUser(authtoken));
    } catch (error) {
      if (error.message === 'Invalid Authentication Token') {
        setUserState(undefined);
      } else {
        throw error;
      }
    }
  }
});

export const getContactNumber = async () => {
  const authtoken = requireAuthtoken();
  return OloGetContactNumber(authtoken);
};

export const updateContactNumber = async (contactNumber: string) => {
  const authtoken = requireAuthtoken();
  await OloUpdateContactNumber(authtoken, contactNumber);
};

export const changePassword = async (currentPassword: string, newPassword: string) => {
  const authtoken = requireAuthtoken();
  await OloChangePassword(authtoken, currentPassword, newPassword);
};

export const updateUser = async (firstname: string, lastname: string, emailaddress: string) => {
  const authtoken = requireAuthtoken();
  setUserState(await OloUpdateUser(authtoken, firstname, lastname, emailaddress));
};

export const getDeliveryAddresses = async () => {
  const authtoken = requireAuthtoken();
  return await OloGetDeliveryAddresses(authtoken);
};

export const deleteDeliveryAddress = async (deliveryAddressId: string) => {
  const authtoken = requireAuthtoken();
  return await OloDeleteDeliveryAddress(authtoken, deliveryAddressId);
}

export function isUserAuthenticated() {
  return userState.status === LoadingStatus.success && userState.data!==undefined && userState.data.type === 'user' && Boolean(userState.data.authtoken);
}

function isUserDefined() {
  return userState.status === LoadingStatus.success && userState.data!==undefined;
}

export async function login(email: string, password: string, basketId: string|undefined) {
  setUserState(await authenticateUser(email, password, basketId));
}

export function createGuestUser() {
  setUserState({
    emailaddress: '',
    firstname: '',
    lastname: '',
    type: 'guest',
    contactnumber: ''
  });
}

export async function logout() {
  const authtoken = requireAuthtoken();
  setUserState(undefined);
  await logoutUser(authtoken);
}

export async function logoutGuestUser() {
  const user = requireUserData();
  if(user.type === 'guest') {
    setUserState(undefined);
  }
}

export async function register(firstname: string, lastname: string, emailaddress: string, password: string, contactnumber: string) {
  setUserState(await createUser(firstname, lastname, emailaddress, password, contactnumber));
}

export const getUserBillingAccounts = async () => {
  const authtoken = requireAuthtoken();
  return OloGetUserBillingAccounts(authtoken);
};

export const deleteUserBillingAccount = async (billingAccountId: string) => {
  const authtoken = requireAuthtoken();
  return OloDeleteUserBillingAccount(authtoken, billingAccountId);
}

export const getRecentOrders = async () => {
  const authtoken = requireAuthtoken();
  return OloGetRecentOrders(authtoken);
}