import find from 'lodash/find';
import trimEnd from 'lodash/trimEnd';
import React, { useEffect, useCallback } from 'react';
import { useHistory, useLocation } from 'react-router-dom';

import { NavigationBar, ErrorPage } from '../Components';

import { withAuthUser } from '../State/User';
import { getBasketData } from '../State/Basket';

import { ProfileContent } from './ProfileContent';
import { OrdersContent } from './OrdersContent';
import { PaymentContent } from './PaymentContent';
import { DeliveryAddressesContent } from './DeliveryAddressesContent';

import { OloUser } from '../OloAPI';

export const ProfilePage = withAuthUser(({ user }: { user: OloUser }) => {
  const basket = getBasketData();

  const sections = [
    { id: 'profile', name: 'My Profile', url: '/profile', content: ProfileContent },
    { id: 'payment', name: 'Payment', url: '/profile/payment', content: PaymentContent },
    { id: 'delivery_addresses', name: 'Addresses', url: '/profile/addressess', content: DeliveryAddressesContent },
    { id: 'order_history', name: 'Order History', url: '/profile/orders', content: OrdersContent }
  ];

  const history = useHistory();
  const location = useLocation();
  const pathname = location.pathname;

  useEffect(() => window.scrollTo(0, 0), [pathname]);

  const updateSection = useCallback((section: { url: string }) => history.push(section.url), [history]);

  const currentSection = find(sections, { url: trimEnd(pathname, '/') });
  if (!currentSection) {
    return <ErrorPage error={new Error("Page not found")} />;
  }
  const ContentComponent = currentSection.content;

  return (
    <div className="profilePage">
      <NavigationBar sectionsData={sections} currentSection={currentSection} updateSection={updateSection} sectionsLayout="tabs"/>
      <div className="content">
        <div className="wrapper">
          <ContentComponent user={user} basket={basket} />;
        </div>
      </div>
    </div>
  );
});
