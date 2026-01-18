import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageBasicViewTab = lazy(() => import('./LitigationCasePageBasicViewTab'));

const LitigationCasePageBasicViewTab = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageBasicViewTab {...props} />
  </Suspense>
);

export default LitigationCasePageBasicViewTab;
