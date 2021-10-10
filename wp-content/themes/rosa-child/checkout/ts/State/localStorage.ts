type LocalStorageKeys = 'userData' | 'basketId';

export function loadValue(key: LocalStorageKeys) {
  const storage = window.localStorage;
  const value = storage.getItem(key);
  return value;
}

export function saveValue(key: LocalStorageKeys, value: string) {
  const storage = window.localStorage;
  storage.setItem(key, value);
}

export function removeValue(key: LocalStorageKeys) {
  const storage = window.localStorage;
  storage.removeItem(key);
}

export function clearValues() {
  const storage = window.localStorage;
  storage.clear();
}
