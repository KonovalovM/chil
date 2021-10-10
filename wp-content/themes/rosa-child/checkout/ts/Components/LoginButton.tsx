import React, { useCallback, useState, useEffect, useRef } from 'react';
import { observer } from 'mobx-react';
import { Link, useHistory } from 'react-router-dom';
import classnames from 'classnames';
import { cleanBasket } from '../State/Basket';
import { UserState, withUserState, isUserAuthenticated, logout } from '../State/User';
import { LoadingStatus } from '../Common/Types';
import { getAsset } from '../Utilities/assetsHelper';
import { setIsLoading, setErrorModalMessage } from '../State/Global';

export const LoginButton = withUserState(
  observer(({ userState }: { userState: UserState }) => {
    const [isOpen, setIsOpen] = useState(false);
    const history = useHistory();
    const containerRef = useRef<HTMLDivElement>(null);

    const handleMousedown = useCallback(
      (event: MouseEvent) => {
        if (containerRef.current && !containerRef.current.contains(event.target as HTMLDivElement)) {
          setIsOpen(false);
        }
      },
      [containerRef, setIsOpen]
    );

    useEffect(() => {
      document.addEventListener('mousedown', handleMousedown);
      return () => document.removeEventListener('mousedown', handleMousedown);
    }, [handleMousedown, isOpen]);

    const openDropdownMenu = useCallback(() => {
      if (isUserAuthenticated()) {
        setIsOpen(!isOpen);
      } else {
        history.push('/auth/sign-in');
        setIsOpen(false);
      }
    }, [isOpen, setIsOpen, history]);

    const logoutHandler = useCallback(async () => {
      try {
        setIsLoading(true);
        await cleanBasket();
        await logout();
        history.push('/');
        setIsLoading(false);
      } catch (error) {
        setIsLoading(false);
        setErrorModalMessage(error);
      }
    }, [history]);

    if (userState.status !== LoadingStatus.success) {
      return null;
    }

    return (
      <div className="loginButton" ref={containerRef}>
        <button onClick={openDropdownMenu}>
          <img src={getAsset('login.svg')} />
        </button>
        {userState.data && (
          <div className={classnames('menu', { visible: isOpen })}>
            <div>{'Hi ' + userState.data.firstname}</div>
            <div>
              <Link to="/profile">My profile</Link>
            </div>
            <div>
              <Link to="/profile/payment">Payment</Link>
            </div>
            <div>
              <Link to="/profile/addressess">Delivery Addresses</Link>
            </div>
            <div>
              <Link to="/profile/orders">Order history</Link>
            </div>
            <div className="link" onClick={logoutHandler}>
              Logout
            </div>
          </div>
        )}
      </div>
    );
  })
);
