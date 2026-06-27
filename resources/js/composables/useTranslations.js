import { usePage } from '@inertiajs/vue3';

function getByPath(obj, path) {
  if (!obj || !path) return undefined;
  const parts = path.split('.');
  let curr = obj;
  for (const p of parts) {
    if (curr && Object.prototype.hasOwnProperty.call(curr, p)) {
      curr = curr[p];
    } else {
      return undefined;
    }
  }
  return curr;
}

export default function useTranslations() {
  const page = usePage();

  function t(key, fallback = '') {
    const dict = page.props?.i18n?.translations || {};
    const val = getByPath(dict, key);
    if (typeof val === 'string') return val;
    return fallback || key;
  }

  return { t };
}
