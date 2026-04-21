let idCounter = 0;

export function useGenerateId(prefix = 'id') {
  const generateId = (suffix = '') => {
    idCounter++;
    if (suffix) {
      return `${prefix}_${suffix}_${idCounter}`;
    }
    return `${prefix}_${idCounter}`;
  };

  return { generateId };
}

export function useDebounce(fn, delay = 300) {
  let timeoutId = null;
  
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
}

export function useThrottle(fn, delay = 300) {
  let lastCall = 0;
  
  return (...args) => {
    const now = Date.now();
    if (now - lastCall >= delay) {
      lastCall = now;
      fn(...args);
    }
  };
}