package tracking.steps

import data.CreatorItems
import database.Creator
import database.CreatorUrl
import database.CreatorUrls
import org.jetbrains.exposed.dao.with
import web.snapshots.Snapshot
import tracking.website.Strategy
import web.snapshots.SnapshotsManager
import java.util.stream.Stream

class SnapshotsProvider(private val snapshotsManager: SnapshotsManager) {
    private val creators: Map<Creator, List<CreatorUrl>> = CreatorUrl
        .find { CreatorUrls.type eq "URL_COMMISSIONS" } // TODO: Enum!
        .with(CreatorUrl::creator)
        .groupBy { it.creator }

    fun getSnapshotsStream(): Stream<CreatorItems<Snapshot>> { // FIXME: All of it
        return creators.entries
            .parallelStream()
            .map { (creator, urls) ->
                CreatorItems(creator, urls.map {
                    val url = Strategy
                        .forUrl(it.url)
                        .coerceUrl(it.url)

                    snapshotsManager.get(url)
                })
            }
    }

    fun getCreators() = creators.keys
}
