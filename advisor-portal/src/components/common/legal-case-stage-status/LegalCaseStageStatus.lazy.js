import React, { lazy, Suspense } from 'react';

const LazyLegalCaseStageStatus = lazy(() => import('./LegalCaseStageStatus'));

const LegalCaseStageStatus = props => (
  <Suspense fallback={null}>
    <LazyLegalCaseStageStatus {...props} />
  </Suspense>
);

export default LegalCaseStageStatus;
