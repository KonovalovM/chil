import { logError } from '../Utilities/errorLogger';
import React, { useEffect } from 'react';
import { Link } from 'react-router-dom';

export function ErrorPage(props: { error: Error }) {
  const { error } = props;
  const message = props.error.message;
  useEffect(() => {
  	logError(error);
  	window.scrollTo(0, 0);
  }, [error]);

  return (
    <div className="errorPage">
      <h1>{message}</h1>
      <Link to="/">Search restaurants</Link>
    </div>
  );
}
