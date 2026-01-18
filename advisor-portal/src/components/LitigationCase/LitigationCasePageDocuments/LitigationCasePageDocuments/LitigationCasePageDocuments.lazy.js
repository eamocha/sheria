import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageDocuments = lazy(() => import('./LitigationCasePageDocuments'));

const LitigationCasePageDocuments = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageDocuments {...props} />
  </Suspense>
);

export default LitigationCasePageDocuments;
