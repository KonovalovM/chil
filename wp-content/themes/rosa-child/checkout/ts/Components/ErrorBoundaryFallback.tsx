import { captureException } from '@sentry/react';
import React, { PureComponent, useCallback } from 'react';

interface ErrorBoundaryBallbackProps {
  error: Error | null;
  componentStack: string | null;
  resetError(): void
}

export function ErrorBoundaryFallback(props: ErrorBoundaryBallbackProps) {

  const errorMessage = props.error ? props.error.message : '';
  const componentStack = props.componentStack;
  const errorStack = props.error ? props.error.stack : '';

  return (
    <div>
      <h1>Uh oh! Something went wrong!</h1>
      <p>
        This application has encountered an error it was not able to recover from. Please provide the following data to our support staff along with a
        description of what you were doing at the time of the error:
      </p>
      <textarea rows={10} cols={100} value={`${errorMessage}\n${errorStack}\n${componentStack}`} readOnly={true} />
      <br />
      <button onClick={()=> {
        props.resetError();
        window.location.reload();
      }}>Reload Page</button>
    </div>
  );
}
