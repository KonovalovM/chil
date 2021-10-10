import React from 'react';
import { IObservableObject } from 'mobx';
import { observer } from 'mobx-react';

import { LoadingStatus } from '../Common/Types';
import { LoadingPage, ErrorPage } from '../Components';

import { usePromise } from './Hooks';

export type ObservableObjectType<Data> = (
  | {
      status: LoadingStatus.progress;
    }
  | {
      error: Error;
      status: LoadingStatus.error;
    }
  | {
      data: Data;
      status: LoadingStatus.success;
    }
) &
  IObservableObject;

export function makeWithObservable<PropName extends string, Data>(propName: PropName, observableObject: ObservableObjectType<Data>) {
  return <PropType extends { [key in PropName]: Data }>(
    WrappedComponent: React.FunctionComponent<PropType>
  ): React.FunctionComponent<Omit<PropType, PropName>> => {
    const ObserverComponent = observer((props: PropType & { observableObject: ObservableObjectType<Data> }) => {
      if (props.observableObject.status === LoadingStatus.progress) {
        return <LoadingPage />;
      } else if (props.observableObject.status === LoadingStatus.error) {
        return <ErrorPage error={props.observableObject.error} />;
      } else {
        return <WrappedComponent {...(props as PropType)} {...{ [propName]: props.observableObject.data }} />;
      }
    });
    return (props: Omit<PropType, PropName>) => {
      return <ObserverComponent {...(props as PropType)} observableObject={observableObject} />;
    };
  };
}

export function makeWithPromise<PropName extends string, Result>(propName: PropName, operation: () => Promise<Result>) {
  return <PropType extends { [key in PropName]: Result }>(
    WrappedComponent: React.FunctionComponent<PropType>
  ): React.FunctionComponent<Omit<PropType, PropName>> => {
    return (props: Omit<PropType, PropName>) => {
      const promise = usePromise(operation, []);
      if (promise.status === LoadingStatus.progress) {
        return <LoadingPage />;
      } else if (promise.status === LoadingStatus.error) {
        return <ErrorPage error={promise.error} />;
      } else {
        return <WrappedComponent {...(props as PropType)} {...{ [propName]: promise.value }} />;
      }
    };
  };
}

export function makeWithPromiseWithProps<PropName extends string, Result, RequiredProps extends {}>(
  propName: PropName,
  operation: (props: RequiredProps) => () => Promise<Result>,
  dependencies?: (props: RequiredProps) => React.DependencyList
) {
  return <PropType extends RequiredProps>(
    WrappedComponent: React.FunctionComponent<PropType & { [key in PropName]: Result }>
  ): React.FunctionComponent<PropType> => {
    return (props: PropType) => {
      const promise = usePromise(operation(props), dependencies ? dependencies(props) : []);
      if (promise.status === LoadingStatus.progress) {
        return <LoadingPage />;
      } else if (promise.status === LoadingStatus.error) {
        return <ErrorPage error={promise.error} />;
      } else {
        return <WrappedComponent {...props} {...({ [propName]: promise.value } as { [key in PropName]: Result })} />;
      }
    };
  };
}
