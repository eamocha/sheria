import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageStagesTab = lazy(() => import('./LitigationCasePageStagesTab'));

const LitigationCasePageStagesTab = props => (
    <Suspense fallback={null}>
        <LazyLitigationCasePageStagesTab {...props} />
    </Suspense>
);

export default LitigationCasePageStagesTab;
