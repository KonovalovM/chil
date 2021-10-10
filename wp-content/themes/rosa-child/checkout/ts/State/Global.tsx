import { logError } from '../Utilities/errorLogger';
import React from 'react';
import { observable, action } from 'mobx';

export interface GlobalState {
  isLoading: boolean;
  errorMessage: string | undefined;
  redirectUrl: string | undefined;
}

const globalState: GlobalState = observable({
  isLoading: false,
  errorMessage: undefined,
  redirectUrl: undefined,
});

export function withGlobalState<PropType extends { globalState: GlobalState }>(
  WrappedComponent: React.FunctionComponent<PropType>
): React.FunctionComponent<Omit<PropType, 'globalState'>> {
  return (props: Omit<PropType, 'globalState'>) => {
    return <WrappedComponent {...(props as PropType)} globalState={globalState} />;
  };
}

export const setIsLoading = action((isLoading: boolean) => {
  globalState.isLoading = isLoading;
});

export const setErrorModalMessage = action((error: Error | undefined) => {
  if(error) {
    logError(error);
  }
  globalState.errorMessage = error ? error.message : undefined;
});

export const getRedirectUrl = () => globalState.redirectUrl;

export const setRedirectUrl = action((redirectUrl: string | undefined) => {
  globalState.redirectUrl = redirectUrl;
});
