import React, { lazy, Suspense } from 'react';

const LazyStageExternalReferencesTable = lazy(() => import('./StageExternalReferencesTable'));

const StageExternalReferencesTable = props => (
  <Suspense fallback={null}>
    <LazyStageExternalReferencesTable {...props} />
  </Suspense>
);

export default StageExternalReferencesTable;
