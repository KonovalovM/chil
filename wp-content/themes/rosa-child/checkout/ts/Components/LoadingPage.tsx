import React, { useEffect } from 'react';

interface LoadingPageProps {
  message?: string;
}

export function LoadingPage(props: LoadingPageProps) {
  const message = props.message || 'Loading ...';

  useEffect(() => window.scrollTo(0, 0), []);

  return (
    <div className="loadingPage">
      <h1>{message}</h1>
    </div>
  );
}
