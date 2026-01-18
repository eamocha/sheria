import React, { lazy, Suspense } from 'react';

const LazyStageExternalReferencesTableRow = lazy(() => import('./StageExternalReferencesTableRow'));

const StageExternalReferencesTableRow = props => (
  <Suspense fallback={null}>
    <LazyStageExternalReferencesTableRow {...props} />
  </Suspense>
);

export default StageExternalReferencesTableRow;
