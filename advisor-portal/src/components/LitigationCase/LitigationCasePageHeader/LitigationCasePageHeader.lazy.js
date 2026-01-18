import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageHeader = lazy(() => import('./LitigationCasePageHeader'));

const LitigationCasePageHeader = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageHeader {...props} />
  </Suspense>
);

export default LitigationCasePageHeader;
