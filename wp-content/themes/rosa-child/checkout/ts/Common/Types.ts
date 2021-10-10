export enum LoadingStatus {
  progress,
  success,
  error,
}

export type PromiseStatus<Result> =
  | {
      status: LoadingStatus.progress;
    }
  | { status: LoadingStatus.success; value: Result }
  | { status: LoadingStatus.error; error: Error };

export enum ProductLayout {
  simple,
  complex,
}