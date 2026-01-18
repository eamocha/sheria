import React, { lazy, Suspense } from 'react';

const LazyAPLegalCaseLitigationDetailPicker = lazy(() => import('./APLegalCaseLitigationDetailPicker'));

const APLegalCaseLitigationDetailPicker = props => (
  <Suspense fallback={null}>
    <LazyAPLegalCaseLitigationDetailPicker {...props} />
  </Suspense>
);

export default APLegalCaseLitigationDetailPicker;
