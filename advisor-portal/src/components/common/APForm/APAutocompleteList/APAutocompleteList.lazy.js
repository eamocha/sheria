import React, { lazy, Suspense } from 'react';

const LazyAPAutocompleteList = lazy(() => import('./APAutocompleteList'));

const APAutocompleteList = props => (
  <Suspense fallback={null}>
    <LazyAPAutocompleteList {...props} />
  </Suspense>
);

export default APAutocompleteList;
