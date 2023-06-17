plugins {
    kotlin("jvm") version "1.8.22"
    kotlin("plugin.serialization") version "1.8.22"
    application

    id("org.jetbrains.kotlinx.kover") version "0.7.1"
    id("jacoco")
}

group = "it.getfursu"
version = "1.0-SNAPSHOT"

repositories {
    mavenCentral()
}

val exposedVersion: String by project
val mockkVersion: String by project
val ktorVersion: String by project

dependencies {
    // JSON and YAML support
    implementation("org.jetbrains.kotlinx:kotlinx-serialization-json:1.3.2")
    implementation("com.fasterxml.jackson.module:jackson-module-kotlin:2.15.0")
    implementation("com.fasterxml.jackson.dataformat:jackson-dataformat-yaml:2.15.0")

    // Logging
    implementation("io.github.oshai:kotlin-logging-jvm:4.0.0-beta-29")
    implementation("org.slf4j:slf4j-simple:2.0.7") // TODO: Use file logging
    implementation("ch.qos.logback:logback-core:1.3.5") // TODO: Use file logging

    // Database
    implementation("org.jetbrains.exposed:exposed-core:$exposedVersion")
    implementation("org.jetbrains.exposed:exposed-dao:$exposedVersion")
    implementation("org.jetbrains.exposed:exposed-java-time:$exposedVersion")
    implementation("org.jetbrains.exposed:exposed-jdbc:$exposedVersion")
    implementation("org.xerial:sqlite-jdbc:3.30.1")

    // HTML parsing
    implementation("org.jsoup:jsoup:1.16.1")

    // HTTP client
    implementation("io.ktor:ktor-client-apache5:$ktorVersion")
    implementation("io.ktor:ktor-client-core:$ktorVersion")
    implementation("io.ktor:ktor-client-logging:$ktorVersion")
    implementation("io.ktor:ktor-client-encoding:$ktorVersion")

    // Tests
    testImplementation(kotlin("test"))
    testImplementation("io.mockk:mockk:$mockkVersion")
}

tasks.test {
    useJUnitPlatform()

    finalizedBy(tasks.jacocoTestReport)
}

tasks.jacocoTestReport {
    dependsOn(tasks.test)

    reports {
        xml.required.set(false)
        html.required.set(true)
        csv.required.set(false)
    }
}

jacoco {
    toolVersion = "0.8.10"
}

kotlin {
    jvmToolchain(17)
}

application {
    mainClass.set("MainKt")
}
