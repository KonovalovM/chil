import React, { useState, useEffect, useRef } from 'react';
import { LoadingStatus, PromiseStatus } from '../Common/Types';

//This is useful to use with React Modal when the state of open/close
//depends of the content
export function useModalValues<ModalValue>(value: ModalValue | undefined): [boolean, ModalValue | undefined] {
  const previousValue = useRef<ModalValue | undefined>();

  useEffect(() => {
    previousValue.current = value;
  }, [value]);

  const isOpen = value !== undefined;
  const finalValue = value !== undefined ? value : previousValue.current;
  return [isOpen, finalValue];
}

export function usePromise<Result>(operation: () => Promise<Result>, dependencies?: React.DependencyList): PromiseStatus<Result> {
  const [status, setStatus] = useState<PromiseStatus<Result>>({ status: LoadingStatus.progress });

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        const value = await operation();
        if (!cancelled) {
          setStatus({ status: LoadingStatus.success, value });
        }
      } catch (error) {
        if (!cancelled) {
          setStatus({ status: LoadingStatus.error, error });
        }
      }
    })();
    return () => {
      cancelled = true;
      setStatus({ status: LoadingStatus.progress });
    };
  }, dependencies);

  return status;
}
