import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageBasicViewCustomFields = lazy(() => import('./LitigationCasePageBasicViewCustomFields'));

const LitigationCasePageBasicViewCustomFields = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageBasicViewCustomFields {...props} />
  </Suspense>
);

export default LitigationCasePageBasicViewCustomFields;
